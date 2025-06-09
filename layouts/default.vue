<template>
    <div class="font-main w-full relative min-h-screen bg-slate-50 flex flex-col">
        <AppHeader />
        <main class="pt-16 flex-grow">
            <slot />
        </main>
        <AppFooter />
        <div class="fixed bottom-0 right-0 p-3 sm:p-5 z-[1000]">
            <Transition name="pop-chat">
                <button v-if="minimized"
                    class="bg-orange-500 rounded-full w-14 h-14 sm:w-16 sm:h-16 flex items-center justify-center shadow-xl hover:bg-orange-600 transition-all duration-300 ease-out transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2"
                    @click="minimized = false" title="Mở chat" aria-label="Mở cửa sổ chat">
                    <LucideMessageCircle class="w-7 h-7 sm:w-8 sm:h-8 text-white" />
                </button>
            </Transition>

            <Transition name="slide-up-chat">
                <div v-if="!minimized"
                    class="w-[calc(100vw-24px)] sm:w-[600px] md:w-[600px] h-[calc(100vh-100px)] max-h-full sm:max-h-[85vh] bg-orange-50 rounded-xl shadow-2xl flex flex-col border-2 border-orange-400 overflow-hidden">
                    <div
                        class="p-3.5 sm:p-4 border-b border-orange-300 flex items-center font-semibold text-orange-800 justify-between bg-orange-100">
                        <div class="flex items-center gap-2.5">
                            <LucideMessageCircle
                                class="w-7 h-7 sm:w-8 sm:h-8 text-orange-600 cursor-pointer hover:text-orange-700 transition-colors"
                                @click="showInfo = true" title="Thông tin Chat Bot" />
                            <span class="text-base sm:text-lg">Chat Bot Tư Vấn</span>
                        </div>
                        <button
                            class="p-1.5 rounded-md hover:bg-orange-200/70 transition-colors text-orange-600 hover:text-orange-700"
                            @click="minimized = true" title="Thu nhỏ" aria-label="Thu nhỏ cửa sổ chat">
                            <LucideChevronDown class="w-5 h-5 sm:w-6 sm:h-6" />
                        </button>
                    </div>

                    <div ref="chatBody" class="flex-1 overflow-y-auto p-3 sm:p-4 space-y-3 bg-white">
                        <TransitionGroup name="message-item" tag="div">
                            <div v-for="msg in messages" :key="msg.id" :class="[
                                'rounded-xl py-2 px-3.5 shadow-md w-max max-w-[85%] sm:max-w-[80%] text-sm sm:text-base leading-relaxed mb-4 break-words',
                                msg.role === 'user'
                                    ? 'bg-orange-500 text-white ml-auto rounded-br-none'
                                    : 'bg-slate-100 text-slate-800 mr-auto rounded-bl-none border border-slate-200',
                            ]">
                                <div class="whitespace-pre-wrap">{{ msg.text }}</div>
                                <div class="whitespace-pre-wrap">{{ msg.noi_dung }}</div>

                                <div v-if="msg.role === 'bot' && msg.books && msg.books.length > 0"
                                    class="mt-3 space-y-3">
                                    <div v-for="(book, bIdx) in msg.books" :key="`book-${bIdx}`"
                                        @click="book.suggestions ? useSuggestion(book.suggestions) : '' "
                                        class="p-2.5 border border-orange-200 rounded-lg bg-orange-50/50 text-xs text-slate-700" 
                                        :class="{'cursor-pointer hover:bg-orange-200 hover:shadow-md transition-all duration-200 ease-out text-left': book.suggestions}">
                                        <p class="font-semibold text-orange-700">
                                            {{ book.tieu_de }} - {{ book.tac_gia }}
                                        </p>
                                        <p class="mt-1">
                                            <span class="font-medium">Thể loại:</span>
                                            {{ book.the_loai }}
                                        </p>
                                        <p class="mt-1">
                                            <span class="font-medium">Mô tả:</span>
                                            {{ book.mo_ta_ngan_gon }}
                                        </p>
                                        <p class="mt-1">
                                            <span class="font-medium">Lý do phù hợp:</span>
                                            {{ book.ly_do_phu_hop }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div v-if="msg.role === 'bot' && msg.bookSuggestions && msg.bookSuggestions.length > 0"
                                    class="mt-2.5 flex flex-col items-start gap-2">
                                    <button v-for="(suggest, sidx) in msg.bookSuggestions" :key="sidx"
                                        class="rounded-full bg-orange-100 px-3.5 py-1.5 text-xs sm:text-sm text-orange-700 border border-orange-300 hover:bg-orange-200 hover:shadow-md transition-all duration-200 ease-out cursor-pointer text-left"
                                        @click="useSuggestion(suggest.fullText || suggest)">
                                        {{ suggest.display || suggest }}
                                    </button>
                                </div>
                                <ul v-if="msg.role === 'bot' && msg.suggestions && msg.suggestions.length > 0"
                                    class="mt-2.5 flex flex-col items-start gap-2 list-disc list-inside text-orange-700 text-xs sm:text-sm">
                                    <li v-for="(suggest, sidx) in msg.suggestions" :key="sidx">
                                        {{ suggest.display || suggest }}
                                    </li>
                                </ul>
                            </div>
                        </TransitionGroup>
                        <Transition name="message-item">
                            <div v-if="isTyping" key="typing-indicator"
                                class="rounded-xl py-2 px-3.5 shadow-md w-max max-w-[85%] sm:max-w-[80%] text-sm sm:text-base leading-relaxed mb-4 bg-slate-100 text-slate-800 mr-auto rounded-bl-none border border-slate-200 flex items-center space-x-1.5">
                                <div class="typing-dot"></div>
                                <div class="typing-dot" style="animation-delay: 0.2s;"></div>
                                <div class="typing-dot" style="animation-delay: 0.4s;"></div>
                            </div>
                        </Transition>
                        <Transition name="message-item">
                            <div v-if="messages.length === 0" key="welcome-message"
                                class="rounded-xl py-2 px-3.5 shadow-md w-max max-w-[85%] sm:max-w-[80%] text-sm sm:text-base leading-relaxed mb-4 bg-slate-100 text-slate-800 mr-auto rounded-bl-none border border-slate-200">
                                <div class="font-semibold mb-2">Xin chào! 👋 Tôi là Chat Bot Tư Vấn Sách.</div>
                                <div>Bạn muốn tìm sách gì hôm nay? Hãy chọn một gợi ý hoặc đặt câu hỏi cho tôi nhé:
                                </div>
                                <div class="mt-3 flex flex-col items-start gap-2">
                                    <button
                                        class="rounded-full bg-orange-100 px-3.5 py-1.5 text-xs sm:text-sm text-orange-700 border border-orange-300 hover:bg-orange-200 hover:shadow-md transition-all duration-200 ease-out cursor-pointer text-left"
                                        @click="useSuggestion('Tôi thích thể loại sách kinh tế chính trị, bạn có thể gợi ý cho tôi không?')">
                                        Tôi thích thể loại sách kinh tế chính trị, bạn có thể gợi ý cho tôi không?
                                    </button>
                                    <button
                                        class="rounded-full bg-orange-100 px-3.5 py-1.5 text-xs sm:text-sm text-orange-700 border border-orange-300 hover:bg-orange-200 hover:shadow-md transition-all duration-200 ease-out cursor-pointer text-left"
                                        @click="useSuggestion('Tôi quan tâm đến sách của tác giả Nguyễn Nhật Ánh, bạn có thể giới thiệu không?')">
                                        Tôi quan tâm đến sách của tác giả Nguyễn Nhật Ánh, bạn có thể giới thiệu không?
                                    </button>
                                    <button
                                        class="rounded-full bg-orange-100 px-3.5 py-1.5 text-xs sm:text-sm text-orange-700 border border-orange-300 hover:bg-orange-200 hover:shadow-md transition-all duration-200 ease-out cursor-pointer text-left"
                                        @click="useSuggestion('Tôi muốn tìm sách phù hợp với lứa tuổi thiếu nhi, bạn có gợi ý nào không?')">
                                        Tôi muốn tìm sách phù hợp với lứa tuổi thiếu nhi, bạn có gợi ý nào không?
                                    </button>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <form @submit.prevent="handleSendMessage"
                        class="p-3 sm:p-4 border-t border-orange-300 flex items-center gap-2 sm:gap-3 bg-orange-50">
                        <input v-model="prompt" :disabled="isTyping"
                            class="flex-1 rounded-lg border border-orange-300 px-3.5 py-2.5 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow"
                            placeholder="Nhập tin nhắn..." aria-label="Nhập tin nhắn chat" />
                        <button type="button"
                            class="p-2 sm:p-2.5 rounded-lg bg-orange-200 hover:bg-orange-300/80 transition-colors text-orange-600 hover:text-orange-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            @click="sendImage" title="Gửi ảnh (chưa hỗ trợ)" :disabled="isTyping" aria-label="Gửi ảnh">
                            <LucideImage class="w-5 h-5 sm:w-6 sm:h-6" />
                        </button>
                        <button type="submit" :disabled="!prompt.trim() || isTyping"
                            class="p-2 sm:p-2.5 rounded-lg bg-orange-500 hover:bg-orange-600 transition-colors text-white disabled:opacity-60 disabled:cursor-not-allowed"
                            title="Gửi tin nhắn" aria-label="Gửi tin nhắn">
                            <LucideSend class="w-5 h-5 sm:w-6 sm:h-6" />
                        </button>
                    </form>
                </div>
            </Transition>
        </div>

        <Transition name="modal-fade">
            <div v-if="showInfo"
                class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[1001] p-4"
                @click.self="showInfo = false" aria-modal="true" role="dialog">
                <div
                    class="bg-white rounded-xl p-6 sm:p-8 shadow-xl max-w-md w-full transform transition-all duration-300 ease-out">
                    <div class="flex items-center gap-3 sm:gap-4 mb-4 sm:mb-6">
                        <LucideMessageCircle
                            class="w-12 h-12 sm:w-14 sm:h-14 text-orange-500 bg-orange-100 rounded-full border-2 border-orange-300 p-2 sm:p-2.5" />
                        <div>
                            <h3 class="font-bold text-lg sm:text-xl text-orange-700">Chat Bot Thông Minh</h3>
                            <p class="text-xs sm:text-sm text-orange-500">AI Model: Gemini (Google)</p>
                        </div>
                    </div>
                    <p class="text-gray-700 text-sm sm:text-base">
                        Đây là trợ lý AI sử dụng mô hình Gemini của Google, được thiết kế để tư vấn và gợi ý sách cho
                        bạn.
                        Hãy thoải mái đặt câu hỏi hoặc chia sẻ sở thích của bạn nhé!
                    </p>
                    <button
                        class="mt-6 sm:mt-8 w-full py-2.5 sm:py-3 rounded-lg bg-orange-500 text-white hover:bg-orange-600 transition-colors focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2"
                        @click="showInfo = false" aria-label="Đóng thông tin chatbot">
                        Đã hiểu
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, nextTick, watch } from 'vue';
// Đảm bảo bạn đã cài đặt lucide-vue-next: npm install lucide-vue-next
import { LucideSend, LucideImage, LucideChevronDown, LucideMessageCircle } from 'lucide-vue-next';

// Components AppHeader và AppFooter được giả định là đã được import toàn cục hoặc trong script này nếu cần
// import AppHeader from '~/components/AppHeader.vue';
// import AppFooter from '~/components/AppFooter.vue';

const prompt = ref('');
const messages = ref([]); // Mỗi message nên có { id: uniqueId, role: 'user'/'bot', text: '...', suggestions: [], books: [] }
const minimized = ref(true); // Mặc định thu nhỏ
const showInfo = ref(false);
const chatBody = ref(null); // Để tự động cuộn
const isTyping = ref(false); // Trạng thái AI đang soạn thảo

// Hàm tạo ID duy nhất đơn giản cho tin nhắn (quan trọng cho key của v-for và TransitionGroup)
const generateUniqueId = () => `msg_${Date.now()}_${Math.random().toString(36).substring(2, 9)}`;

// Hàm sinh gợi ý hợp lý cho chatbot về sách
function extractFallbackSuggestions(replyText) {
    if (!replyText) {
        return [
            'Bạn muốn tìm sách thuộc thể loại nào?',
            'Bạn quan tâm đến tác giả hoặc chủ đề nào?',
            'Bạn cần gợi ý sách phù hợp với lứa tuổi hoặc mục đích nào?'
        ];
    }
    const lower = replyText.toLowerCase();
    if (lower.includes('xin chào') || lower.includes('chào bạn')) {
        return [
            'Tôi muốn được gợi ý sách hay',
            'Bạn có thể giới thiệu sách theo sở thích không?',
            'Có sách nào phù hợp với tôi không?'
        ];
    }
    if (lower.includes('thể loại')) {
        return [
            'Gợi ý sách thể loại trinh thám',
            'Tôi thích sách khoa học viễn tưởng',
            'Có sách nào về phát triển bản thân không?'
        ];
    }
    if (lower.includes('tác giả')) {
        return [
            'Giới thiệu sách của tác giả nổi tiếng',
            'Tôi muốn tìm sách của Nguyễn Nhật Ánh',
            'Có sách nào của Dan Brown không?'
        ];
    }
    return [
        'Bạn thích thể loại sách nào?',
        'Bạn muốn tìm sách cho mục đích gì?',
        'Bạn có muốn gợi ý sách bán chạy không?'
    ];
}

const scrollToBottom = async () => {
    await nextTick();
    if (chatBody.value) {
        chatBody.value.scrollTop = chatBody.value.scrollHeight;
    }
};

watch(messages, scrollToBottom, { deep: true });

const handleSendMessage = async () => {
    if (!prompt.value.trim() || isTyping.value) return;

    const userMessageText = prompt.value;
    messages.value.push({
        id: generateUniqueId(),
        role: 'user',
        text: userMessageText,
    });
    prompt.value = ''; // Xóa input sau khi gửi
    await scrollToBottom(); // Cuộn xuống ngay sau khi user gửi

    isTyping.value = true; // Báo AI đang soạn

    try {
        // Gọi API endpoint đã tạo ở server/api/chat.post.js
        const responseData = await $fetch('/api/chat', {
            method: 'POST',
            body: { prompt: userMessageText },
        });

        let botReplyText = responseData.reply_text || "Xin lỗi, tôi chưa hiểu ý bạn.";
        let suggestionsForBot = []; // goi y sach
        let bookSuggestions = []; // goi y cau hoi bot
        let booksForBot = [];
        let noi_dung = responseData?.noi_dung || "";

        if (responseData.goi_y_cau_hoi && Array.isArray(responseData.goi_y_cau_hoi) && responseData.goi_y_cau_hoi.length > 0) {
            suggestionsForBot = responseData.goi_y_cau_hoi
        }
        if (responseData.goi_y_sach && Array.isArray(responseData.goi_y_sach) && responseData.goi_y_sach.length > 0) {
            // Sử dụng thông tin chi tiết từ goi_y_sach để hiển thị
            booksForBot = responseData.goi_y_sach.map(sach => ({
                tieu_de: sach.tieu_de,
                tac_gia: sach.tac_gia,
                the_loai: sach.the_loai,
                mo_ta_ngan_gon: sach.mo_ta_ngan_gon,
                ly_do_phu_hop: sach.ly_do_phu_hop,
                suggestions: `Hãy cho tôi biết thêm về sách ${sach.tieu_de}`
            }));
        } else if (responseData.reply_text) { // Nếu không có sách nhưng có reply_text
            bookSuggestions = extractFallbackSuggestions(responseData.reply_text).map(s => ({ display: s, fullText: s }));
        } else { // Trường hợp lỗi hoặc không có gì cả
            botReplyText = responseData.error_details || "Có lỗi xảy ra, vui lòng thử lại sau.";
            bookSuggestions = extractFallbackSuggestions(null).map(s => ({ display: s, fullText: s }));
        }
        if (responseData.error_details) { // Nếu API trả về lỗi cụ thể
            botReplyText = `${botReplyText} (Lỗi: ${responseData.error_details})`;
        }


        messages.value.push({
            id: generateUniqueId(),
            role: 'bot',
            text: botReplyText,
            bookSuggestions: bookSuggestions,
            suggestions: suggestionsForBot,
            books: booksForBot, // Thêm thông tin sách vào message
            noi_dung: noi_dung
        });

    } catch (error) {
        console.error("Lỗi khi gọi API /api/chat:", error);
        let errorMessage = 'Xin lỗi, đã có lỗi xảy ra khi kết nối với AI.';
        // Xử lý lỗi từ $fetch (thường có trong error.data)
        if (error.data && error.data.message) {
            errorMessage = error.data.message;
        } else if (error.statusMessage) {
            errorMessage = error.statusMessage;
        }
        messages.value.push({
            id: generateUniqueId(),
            role: 'bot',
            text: errorMessage,
            bookSuggestions: extractFallbackSuggestions(null).map(s => ({ display: s, fullText: s })),
            suggestions: [],
        });
    } finally {
        isTyping.value = false; // AI đã soạn xong (hoặc lỗi)
        await scrollToBottom(); // Cuộn lại sau khi AI trả lời
    }
};

const sendImage = () => {
    // alert('Chức năng gửi ảnh hiện chưa được hỗ trợ.');
    messages.value.push({
        id: generateUniqueId(),
        role: 'bot',
        text: 'Xin lỗi, chức năng gửi ảnh hiện chưa được hỗ trợ trong bản demo này.',
        suggestions: extractFallbackSuggestions(null).map(s => ({ display: s, fullText: s }))
    })
};

const useSuggestion = (suggestText) => {
    prompt.value = suggestText;
    handleSendMessage(); // Tự động gửi tin nhắn khi chọn gợi ý
};

// Tự động mở chat lần đầu (có thể bỏ nếu không muốn)
// onMounted(() => {
//   setTimeout(() => {
//     minimized.value = false;
//   }, 1000);
// });
</script>

<style>
/* Animations cho Chatbox và Icon */
.pop-chat-enter-active,
.pop-chat-leave-active {
    transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55), opacity 0.3s ease;
}

.pop-chat-enter-from,
.pop-chat-leave-to {
    transform: scale(0.5);
    opacity: 0;
}

.slide-up-chat-enter-active {
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.slide-up-chat-leave-active {
    transition: all 0.3s cubic-bezier(0.55, 0.09, 0.68, 0.53);
}

.slide-up-chat-enter-from,
.slide-up-chat-leave-to {
    transform: translateY(80px) scale(0.95);
    opacity: 0;
}

/* Animations cho danh sách tin nhắn */
.message-item-enter-active {
    transition: all 0.4s ease-out;
}

.message-item-leave-active {
    transition: all 0.2s ease-in;
    /* position: absolute; Dùng cẩn thận nếu gây nhảy layout */
}

.message-item-enter-from {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
}

.message-item-leave-to {
    opacity: 0;
    transform: translateX(30px) scale(0.9);
}

.message-item-move {
    transition: transform 0.4s ease;
}

/* Animation cho Modal */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter-active>div,
/* Target the inner div for transform */
.modal-fade-leave-active>div {
    transition: transform 0.3s ease-out;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

.modal-fade-enter-from>div,
.modal-fade-leave-to>div {
    transform: scale(0.90) translateY(15px);
}

/* Typing Indicator Animation */
.typing-dot {
    width: 8px;
    height: 8px;
    background-color: #f97316;
    /* orange-500 */
    border-radius: 50%;
    animation: typing-blink 1.4s infinite both;
}

@keyframes typing-blink {
    0% {
        opacity: 0.2;
    }

    20% {
        opacity: 1;
    }

    100% {
        opacity: 0.2;
    }
}

/* Custom Scrollbar (Tùy chọn, có thể cần tinh chỉnh thêm) */
/* Dành cho Webkit browsers (Chrome, Safari, Edge mới) */
.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #fff7ed;
    /* orange-50 */
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #fb923c;
    /* orange-400 */
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #f97316;
    /* orange-500 */
}

/* Dành cho Firefox */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #fb923c #fff7ed;
}
</style>