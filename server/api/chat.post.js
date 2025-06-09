// ~/server/api/chat.post.js
import { GoogleGenerativeAI } from "@google/generative-ai";

const config = useRuntimeConfig();
const API_KEY = config.googleAiApiKey;

if (!API_KEY) {
  console.error("Lỗi nghiêm trọng: GEMINI_API_KEY chưa được cấu hình trong runtimeConfig của server.");
  throw createError({
    statusCode: 500,
    statusMessage: "Lỗi cấu hình AI service. Vui lòng liên hệ quản trị viên.",
  });
}

const genAI = new GoogleGenerativeAI(API_KEY);
const model = genAI.getGenerativeModel({ model: "gemini-2.0-flash" }); // Hoặc model bạn muốn dùng

export default defineEventHandler(async (event) => {
  const body = await readBody(event);
  const userPrompt = body.prompt;

  if (!userPrompt || typeof userPrompt !== 'string' || userPrompt.trim() === "") {
    throw createError({
      statusCode: 400,
      statusMessage: "Nội dung prompt không được để trống.",
    });
  }

  // Prompt được cải tiến để yêu cầu cả reply_text và goi_y_sach
  const fullPromptText = `
  Bạn là một AI trợ lý tư vấn sản phẩm sách, nói chuyện tự nhiên, thân thiện và chuyên nghiệp.
  Nhiệm vụ của bạn là đưa ra một lời phản hồi ngắn gọn và gợi ý những cuốn sách phù hợp dựa trên thông tin người dùng cung cấp.

  ❗️ QUAN TRỌNG:
  - KHÔNG ĐƯỢC chào hỏi quá trang trọng ở lời dẫn (ví dụ: "Xin chào quý khách").
  - KHÔNG GIẢI THÍCH thêm về vai trò của bạn trừ khi được hỏi.
  - KHÔNG bao quanh phản hồi JSON bằng \`\`\`json, \`\`\`, hoặc bất kỳ ký tự đánh dấu nào khác.
  - LUÔN LUÔN chỉ trả về một chuỗi JSON **HỢP LỆ** và DUY NHẤT.

  Định dạng JSON mong muốn:
  {
    "reply_text": "Dựa trên sở thích của bạn về [chủ đề người dùng nhắc đến], đây là một vài gợi ý sách có thể bạn sẽ thích:",
    // đưa ra câu trả lời phù hợp lịch sự không nên trả lời lại khách hàng bằng các câu hỏi

    // Có thể đề xuất từ 2 đến 4 cuốn sách, mỗi cuốn theo định dạng tương tự. Nhưng nếu đang hỏi về 1 cuốn sách thì bỏ qua phần này.
    "goi_y_sach": [
      {
        "tieu_de": "Tên sách gợi ý cụ thể",
        "tac_gia": "Tên tác giả cuốn sách",
        "the_loai": "Thể loại chính (ví dụ: Khoa học viễn tưởng, Lịch sử, Tiểu thuyết, Phát triển bản thân...)",
        "mo_ta_ngan_gon": "Tóm tắt RẤT ngắn gọn (1-2 câu) nội dung chính và điểm hấp dẫn của cuốn sách.",
        "ly_do_phu_hop": "Giải thích NGẮN GỌN (1 câu) tại sao sách này phù hợp với thông tin người dùng đã cung cấp."
      }
    ], 
    // nếu người dùng muốn tìm hiểu thêm thì hãy thêm ở đây
    "noi_dung": "",
    "goi_y_cau_hoi": [
      "Thể loại sách yêu thích của bạn là gì?",
      // viết thêm vào đây tối đa 3 câu hỏi
    ]
  }

  Thông tin người dùng cung cấp: "${userPrompt}"
  `;

  try {
    const result = await model.generateContent(fullPromptText);
    const response = result.response;
    const rawText = response.text();

    if (!rawText) {
      console.warn("Gemini API trả về phản hồi trống.");
      throw new Error("Không tìm thấy nội dung phản hồi từ AI.");
    }

    // Cố gắng làm sạch nếu có ký tự thừa (dù prompt đã yêu cầu không có)
    const cleanedText = rawText
      .trim()
      .replace(/^```json\s*/i, "")
      .replace(/^```/, "")
      .replace(/```$/, "")
      .trim();

    try {
      const parsedJson = JSON.parse(cleanedText);
      return parsedJson; // Trả về JSON đã parse cho client
    } catch (parseError) {
      console.error("Lỗi parse JSON từ Gemini:", parseError, "Raw response:", cleanedText);
      // Nếu parse lỗi, có thể AI không trả về đúng JSON.
      // Trả về một cấu trúc lỗi thân thiện hơn hoặc chỉ phần text nếu có thể.
      return {
        reply_text: "Xin lỗi, tôi gặp chút khó khăn khi xử lý yêu cầu này. Bạn có thể thử lại với một câu hỏi khác được không?",
        goi_y_sach: [],
        error_details: "Phản hồi từ AI không phải là JSON hợp lệ.",
        raw_response_for_debug: cleanedText // Chỉ cho debug
      };
    }
  } catch (error) {
    console.error("Lỗi khi gọi Gemini API:", error.message || error);
    // Check for specific Gemini error structures if available from the SDK
    // For now, a generic error message.
    throw createError({
      statusCode: 500,
      statusMessage: `Lỗi khi giao tiếp với AI: ${error.message || 'Unknown error'}`
    });
  }
});