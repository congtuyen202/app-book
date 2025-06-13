import axios from 'axios';

const GEMINI_API_URL = process.env.NUXT_PUBLIC_GEMINI_API_URL || '';

const geminiApi = axios.create({
    baseURL: GEMINI_API_URL,
    headers: {
        'Content-Type': 'application/json',
    }
});

// Thêm interceptor để tự động thêm token nếu có
geminiApi.interceptors.request.use(
    (config) => {
        const token = process.env.NUXT_PUBLIC_GEMINI_API_TOKEN || ''; // hoặc lấy từ localStorage/sessionStorage nếu cần
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

// Hàm gọi Gemini để sinh nội dung
export async function generateGeminiContent(prompt) {
    try {
        const response = await geminiApi.post('', {
            contents: [
                { parts: [{ text: prompt }] }
            ]
        });
        return response.data;
    } catch (error) {
        throw error;
    }
}

export default geminiApi;
