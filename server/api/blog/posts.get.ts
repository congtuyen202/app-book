import { generateGeminiContent } from '~/server/Axios';
import type { BlogPost } from '~/types/models';
import { H3Error } from 'h3';

// Helper to generate slug (simplified)
const generateSlug = (title: string): string => {
  return title.toLowerCase().replace(/\s+/g, '-').replace(/[^\w-]+/g, '');
};

export default defineEventHandler(async (event) => {
  const prompt = `
    Please generate an array of 5 unique blog post objects suitable for a book application blog.
    Each object must strictly follow this JSON structure:
    {
      "title": "string (unique, engaging blog post title)",
      "summary": "string (concise summary, 100-150 words)",
      "category": "string (e.g., 'Book Reviews', 'Reading Tips', 'Author Spotlights', 'Literary Worlds', 'Tech & Books')",
      "tags": ["string", "string", "string"] (3-5 relevant tags)
    }

    Additionally, for the FIRST blog post object in the array, also include a "fullContent" field:
    "fullContent": "string (well-structured article of 3-5 paragraphs in HTML format. Use <p>, <h2>, <h3>, <ul>, <li>, <strong>, <em> tags appropriately. Ensure it's engaging and relevant to readers and book lovers.)"

    For the SECOND blog post object, also include a "fullContent" field with a different article of 3-5 paragraphs in HTML format.

    For the remaining 3 blog posts, the "fullContent" field should be an empty string or not present.
    Do not include any markdown like \`\`\`json or explanations outside the JSON array itself.
    The entire response should be a single valid JSON array of these objects.
  `;

  try {
    const geminiResponse: any = await generateGeminiContent(prompt);

    let rawJsonString = geminiResponse?.candidates?.[0]?.content?.parts?.[0]?.text;

    if (!rawJsonString) {
      console.error("Gemini response missing expected text part:", JSON.stringify(geminiResponse, null, 2));
      throw new H3Error('Failed to get valid content structure from AI', { statusCode: 500 });
    }

    rawJsonString = rawJsonString.replace(/^```json\s*|```$/g, '').trim();

    let generatedPostsData: Array<{
      title: string;
      summary: string;
      category: string;
      tags: string[];
      fullContent?: string;
    }>;

    try {
        generatedPostsData = JSON.parse(rawJsonString);
    } catch (e: any) {
        console.error("Failed to parse JSON from Gemini:", e.message);
        console.error("Raw Gemini JSON string:", rawJsonString);
        throw new H3Error('Failed to parse blog data from AI', { statusCode: 500 });
    }

    if (!Array.isArray(generatedPostsData)) {
        console.error("Parsed data is not an array:", generatedPostsData);
        throw new H3Error('AI did not return an array of posts', { statusCode: 500 });
    }

    const blogPosts: BlogPost[] = generatedPostsData.map((postData, index) => {
      const slug = generateSlug(postData.title);
      const publishedDate = new Date(Date.now() - index * 24 * 60 * 60 * 1000).toISOString(); // Stagger dates slightly
      const metaDescription = postData.summary.substring(0, 160);
      const metaTitle = postData.title;

      return {
        id: `${slug}-${new Date(publishedDate).getTime()}`, // Unique ID based on slug and time
        slug: slug,
        title: postData.title,
        summary: postData.summary,
        content: postData.fullContent || `<p>${postData.summary}</p><p><em>(Full content coming soon...)</em></p>`,
        imageUrl: `https://source.unsplash.com/random/800x450/?${postData.category?.toLowerCase().replace(' ','') || 'book'}&sig=${index}`,
        author: 'MyAppBot AI',
        publishedDate: publishedDate,
        category: postData.category,
        tags: postData.tags,
        metaTitle: metaTitle,
        metaDescription: metaDescription,
      };
    });

    return blogPosts;

  } catch (error: any) {
    console.error('Error fetching blog posts from Gemini:', error.message);
    if (error.isH3Error) { // Check if it's already an H3Error
        throw error;
    }
    // Create a new H3Error with appropriate status code and message
    throw new H3Error('Failed to generate blog posts via AI.', {
        statusCode: 500,
        statusMessage: 'AI Content Generation Error',
        data: { originalError: error.message }
    });
  }
});
