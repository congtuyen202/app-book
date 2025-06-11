<template>
    <div class="font-main w-full relative min-h-screen bg-background flex flex-col">
        <AppHeader />
        <main class="pt-16 flex-grow">
            <slot />
        </main>
        <AppFooter />
        <!-- Chat Popup Button -->
        <div class="fixed bottom-0 right-0 p-3 sm:p-5 z-[1000]">
            <Transition name="pop-chat">
                 <Button v-if="minimized"
                    variant="default"
                    size="icon"
                    class="rounded-full w-14 h-14 sm:w-16 sm:h-16 shadow-lg hover:scale-110 transition-transform duration-300 ease-out"
                    @click="minimized = false"
                    title="Open Chat"
                    aria-label="Open chat window">
                    <LucideMessageCircle class="w-7 h-7 sm:w-8 sm:h-8" />
                </Button>
            </Transition>

            <!-- Chat Window -->
            <Transition name="slide-up-chat">
                <Card v-if="!minimized"
                    class="w-[calc(100vw-24px)] sm:w-[450px] md:w-[450px] h-[calc(100vh-100px)] max-h-full sm:max-h-[75vh] flex flex-col shadow-2xl border-border">
                    <CardHeader class="flex flex-row items-center justify-between p-3 sm:p-4 border-b">
                        <div class="flex items-center gap-2 sm:gap-2.5">
                            <LucideMessageCircle class="w-6 h-6 sm:w-7 sm:h-7 text-primary cursor-pointer hover:opacity-80"
                                @click="showInfo = true" title="Chat Bot Information" />
                            <CardTitle class="text-base sm:text-lg font-semibold">Chat Bot</CardTitle>
                        </div>
                        <Button variant="ghost" size="icon" class="text-muted-foreground hover:text-foreground"
                            @click="minimized = true" title="Minimize" aria-label="Minimize chat window">
                            <LucideChevronDown class="w-5 h-5 sm:w-6 sm:h-6" />
                        </Button>
                    </CardHeader>

                    <CardContent ref="chatBody" class="flex-1 overflow-y-auto p-3 sm:p-4 space-y-3 bg-background">
                        <TransitionGroup name="message-item" tag="div">
                            <div v-for="msg in messages" :key="msg.id" :class="[
                                'rounded-lg py-2 px-3 shadow-sm w-max max-w-[85%] sm:max-w-[80%] text-sm sm:text-base leading-relaxed mb-3 break-words',
                                msg.role === 'user'
                                    ? 'bg-primary text-primary-foreground ml-auto rounded-br-none'
                                    : 'bg-muted text-muted-foreground mr-auto rounded-bl-none border',
                            ]">
                                <div class="whitespace-pre-wrap">{{ msg.text }}</div>
                                <div class="whitespace-pre-wrap">{{ msg.noi_dung }}</div>

                                <!-- Book suggestions in messages -->
                                <div v-if="msg.role === 'bot' && msg.books && msg.books.length > 0"
                                    class="mt-2.5 space-y-2">
                                    <div v-for="(book, bIdx) in msg.books" :key="`book-${bIdx}`"
                                        @click="book.suggestions ? useSuggestion(book.suggestions) : '' "
                                        class="p-2 border border-border rounded-md bg-background/50 text-xs text-foreground"
                                        :class="{'cursor-pointer hover:bg-accent hover:shadow-sm transition-all duration-200 ease-out text-left': book.suggestions}">
                                        <p class="font-semibold text-primary/90">
                                            {{ book.tieu_de }} - {{ book.tac_gia }}
                                        </p>
                                        <p class="mt-0.5"><span class="font-medium">Thể loại:</span> {{ book.the_loai }}</p>
                                        <p class="mt-0.5"><span class="font-medium">Mô tả:</span> {{ book.mo_ta_ngan_gon }}</p>
                                        <p class="mt-0.5"><span class="font-medium">Lý do:</span> {{ book.ly_do_phu_hop }}</p>
                                    </div>
                                </div>
                                
                                <!-- Quick reply buttons for bot messages -->
                                <div v-if="msg.role === 'bot' && msg.bookSuggestions && msg.bookSuggestions.length > 0"
                                    class="mt-2 flex flex-wrap gap-1.5">
                                    <Button v-for="(suggest, sidx) in msg.bookSuggestions" :key="`bs-${sidx}`"
                                        variant="outline" size="sm"
                                        class="text-xs h-auto py-1 px-2.5"
                                        @click="useSuggestion(suggest.fullText || typeof suggest === 'string' ? suggest : suggest.display)">
                                        {{ typeof suggest === 'string' ? suggest : suggest.display }}
                                    </Button>
                                </div>
                                <ul v-if="msg.role === 'bot' && msg.suggestions && msg.suggestions.length > 0 && !(msg.bookSuggestions && msg.bookSuggestions.length >0)"
                                    class="mt-2 flex flex-wrap gap-1.5 list-none p-0">
                                    <li v-for="(suggest, sidx) in msg.suggestions" :key="`s-${sidx}`">
                                         <Button variant="outline" size="sm"
                                            class="text-xs h-auto py-1 px-2.5"
                                            @click="useSuggestion(typeof suggest === 'string' ? suggest : suggest.fullText || suggest.display)">
                                            {{ typeof suggest === 'string' ? suggest : suggest.display }}
                                        </Button>
                                    </li>
                                </ul>
                            </div>
                        </TransitionGroup>
                         <!-- Typing Indicator -->
                        <Transition name="message-item">
                            <div v-if="isTyping" key="typing-indicator"
                                class="rounded-lg py-2 px-3.5 shadow-sm w-max max-w-[85%] sm:max-w-[80%] text-sm sm:text-base leading-relaxed mb-3 bg-muted text-muted-foreground mr-auto rounded-bl-none border flex items-center space-x-1.5">
                                <div class="typing-dot bg-primary"></div>
                                <div class="typing-dot bg-primary" style="animation-delay: 0.2s;"></div>
                                <div class="typing-dot bg-primary" style="animation-delay: 0.4s;"></div>
                            </div>
                        </Transition>
                        <!-- Welcome Message & Initial Suggestions -->
                        <Transition name="message-item">
                            <div v-if="messages.length === 0 && !isTyping" key="welcome-message"
                                class="rounded-lg py-2.5 px-3.5 shadow-sm w-max max-w-[90%] text-sm sm:text-base leading-relaxed mb-3 bg-muted text-muted-foreground mr-auto rounded-bl-none border">
                                <div class="font-semibold mb-1.5">Xin chào! 👋 Tôi là Chat Bot Tư Vấn Sách.</div>
                                <div>Bạn muốn tìm sách gì hôm nay? Hãy chọn một gợi ý hoặc đặt câu hỏi cho tôi nhé:</div>
                                <div class="mt-2.5 flex flex-col items-start gap-2">
                                    <Button variant="outline" size="sm" class="text-xs h-auto py-1 px-2.5 text-left" @click="useSuggestion('Tôi thích thể loại sách kinh tế chính trị, bạn có thể gợi ý cho tôi không?')">
                                        Gợi ý sách kinh tế chính trị?
                                    </Button>
                                    <Button variant="outline" size="sm" class="text-xs h-auto py-1 px-2.5 text-left" @click="useSuggestion('Tôi quan tâm đến sách của tác giả Nguyễn Nhật Ánh, bạn có thể giới thiệu không?')">
                                        Sách của Nguyễn Nhật Ánh?
                                    </Button>
                                    <Button variant="outline" size="sm" class="text-xs h-auto py-1 px-2.5 text-left" @click="useSuggestion('Tôi muốn tìm sách phù hợp với lứa tuổi thiếu nhi, bạn có gợi ý nào không?')">
                                        Sách cho thiếu nhi?
                                    </Button>
                                </div>
                            </div>
                        </Transition>
                    </CardContent>

                    <CardFooter class="p-2 sm:p-3 border-t flex items-center gap-1.5 sm:gap-2">
                        <Input v-model="prompt" :disabled="isTyping"
                            class="flex-1 text-sm sm:text-base py-2 px-3 h-10 sm:h-11"
                            placeholder="Nhập tin nhắn..." aria-label="Chat message input" @keyup.enter="handleSendMessage" />
                        <Button type="button" variant="outline" size="icon"
                            class="h-10 w-10 sm:h-11 sm:w-11 text-muted-foreground hover:text-foreground disabled:opacity-50"
                            @click="sendImage" title="Send image (not supported)" :disabled="isTyping" aria-label="Send image">
                            <LucideImage class="w-5 h-5" />
                        </Button>
                        <Button type="submit" :disabled="!prompt.trim() || isTyping"
                            class="h-10 w-16 sm:h-11 sm:w-20 disabled:opacity-60"
                            title="Send message" aria-label="Send message" @click="handleSendMessage">
                            <LucideSend class="w-5 h-5" />
                        </Button>
                    </CardFooter>
                </Card>
            </Transition>
        </div>

        <!-- Info Modal -->
        <Transition name="modal-fade">
            <div v-if="showInfo"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[1001] p-4"
                @click.self="showInfo = false" aria-modal="true" role="dialog">
                <Card class="w-full max-w-md bg-card text-card-foreground transform transition-all duration-300 ease-out">
                    <CardHeader class="items-center text-center pb-4">
                        <LucideMessageCircle class="w-12 h-12 text-primary mb-2" />
                        <CardTitle class="text-xl">Chat Bot Thông Minh</CardTitle>
                        <CardDescription>AI Model: Gemini (Google)</CardDescription>
                    </CardHeader>
                    <CardContent class="text-sm text-center">
                        <p>
                            Đây là trợ lý AI sử dụng mô hình Gemini của Google, được thiết kế để tư vấn và gợi ý sách cho bạn.
                            Hãy thoải mái đặt câu hỏi hoặc chia sẻ sở thích của bạn nhé!
                        </p>
                    </CardContent>
                    <CardFooter>
                        <Button class="w-full mt-2" @click="showInfo = false" aria-label="Close chat information modal">
                            Đã hiểu
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </Transition>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { ref, nextTick, watch, Ref } from 'vue';
// Đảm bảo bạn đã cài đặt lucide-vue-next: npm install lucide-vue-next
// Lucide icons are auto-imported by nuxt-lucide-icons module, but explicit import can be kept for clarity or if issues arise.
// import { LucideSend, LucideImage, LucideChevronDown, LucideMessageCircle } from 'lucide-vue-next';

// Components AppHeader và AppFooter được giả định là đã được import toàn cục hoặc trong script này nếu cần
// AppHeader, AppFooter, NuxtLink, Transition, TransitionGroup are auto-imported by Nuxt

interface MessageBook {
  tieu_de: string;
  tac_gia: string;
  the_loai: string;
  mo_ta_ngan_gon: string;
  ly_do_phu_hop: string;
  suggestions?: string;
}

interface MessageSuggestion {
  display: string;
  fullText: string;
}

// Combined type for suggestions to match existing logic
type SuggestionItem = string | { display: string; fullText?: string };

interface Message {
  id: string;
  role: 'user' | 'bot';
  text?: string;
  noi_dung?: string;
  books?: MessageBook[];
  bookSuggestions?: MessageSuggestion[];
  suggestions?: SuggestionItem[];
}

const prompt: Ref<string> = ref('');
const messages: Ref<Message[]> = ref([]);
const minimized: Ref<boolean> = ref(true);
const showInfo: Ref<boolean> = ref(false);
const chatBody: Ref<HTMLElement | null> = ref(null);
const isTyping: Ref<boolean> = ref(false);

// Hàm tạo ID duy nhất đơn giản cho tin nhắn (quan trọng cho key của v-for và TransitionGroup)
const generateUniqueId = (): string => `msg_${Date.now()}_${Math.random().toString(36).substring(2, 9)}`;

// Hàm sinh gợi ý hợp lý cho chatbot về sách
function extractFallbackSuggestions(replyText?: string | null): string[] {
    if (!replyText) {
        return [
            'Bạn muốn tìm sách thuộc thể loại nào?',
            'Bạn quan tâm đến tác giả hoặc chủ đề nào?',
            'Bạn cần gợi ý sách phù hợp với lứa tuổi hoặc mục đích nào?'
        ];
    }
    const lower: string = replyText.toLowerCase();
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

const scrollToBottom = async (): Promise<void> => {
    await nextTick();
    if (chatBody.value) {
        chatBody.value.scrollTop = chatBody.value.scrollHeight;
    }
};

watch(messages, scrollToBottom, { deep: true });

const handleSendMessage = async (): Promise<void> => {
    if (!prompt.value.trim() || isTyping.value) return;

    const userMessageText: string = prompt.value;
    messages.value.push({
        id: generateUniqueId(),
        role: 'user',
        text: userMessageText,
    });
    prompt.value = ''; // Xóa input sau khi gửi
    await scrollToBottom(); // Cuộn xuống ngay sau khi user gửi

    isTyping.value = true; // Báo AI đang soạn

    try {
        // Define expected response structure from API
        interface ApiResponse {
          reply_text?: string;
          noi_dung?: string;
          goi_y_cau_hoi?: SuggestionItem[];
          goi_y_sach?: MessageBook[];
          error_details?: string;
        }

        const responseData = await $fetch<ApiResponse>('/api/chat', {
            method: 'POST',
            body: { prompt: userMessageText },
        });

        let botReplyText: string = responseData.reply_text || "Xin lỗi, tôi chưa hiểu ý bạn.";
        let suggestionsForBot: SuggestionItem[] = [];
        let bookSuggestionsForBot: MessageSuggestion[] = [];
        let booksForBot: MessageBook[] = [];
        let noi_dung_bot: string = responseData?.noi_dung || "";

        if (responseData.goi_y_cau_hoi && Array.isArray(responseData.goi_y_cau_hoi) && responseData.goi_y_cau_hoi.length > 0) {
            suggestionsForBot = responseData.goi_y_cau_hoi;
        }
        if (responseData.goi_y_sach && Array.isArray(responseData.goi_y_sach) && responseData.goi_y_sach.length > 0) {
            booksForBot = responseData.goi_y_sach.map(sach => ({
                ...sach, // Spread existing properties
                suggestions: `Hãy cho tôi biết thêm về sách ${sach.tieu_de}` // Add suggestions if not present
            }));
        } else if (responseData.reply_text) {
            bookSuggestionsForBot = extractFallbackSuggestions(responseData.reply_text).map(s => ({ display: s, fullText: s }));
        } else {
            botReplyText = responseData.error_details || "Có lỗi xảy ra, vui lòng thử lại sau.";
            bookSuggestionsForBot = extractFallbackSuggestions(null).map(s => ({ display: s, fullText: s }));
        }
        if (responseData.error_details) {
            botReplyText = `${botReplyText} (Lỗi: ${responseData.error_details})`;
        }

        messages.value.push({
            id: generateUniqueId(),
            role: 'bot',
            text: botReplyText,
            bookSuggestions: bookSuggestionsForBot,
            suggestions: suggestionsForBot,
            books: booksForBot,
            noi_dung: noi_dung_bot,
        });

    } catch (error: any) { // Catch block with typed error
        console.error("Lỗi khi gọi API /api/chat:", error);
        let errorMessage = 'Xin lỗi, đã có lỗi xảy ra khi kết nối với AI.';

        if (error.data && error.data.message) {
            errorMessage = error.data.message;
        } else if (error.statusMessage) {
            errorMessage = error.statusMessage;
        } else if (error.message) {
            errorMessage = error.message;
        }

        messages.value.push({
            id: generateUniqueId(),
            role: 'bot',
            text: errorMessage,
            bookSuggestions: extractFallbackSuggestions(null).map(s => ({ display: s, fullText: s })),
            suggestions: [], // Ensure suggestions is an empty array
        });
    } finally {
        isTyping.value = false;
        await scrollToBottom();
    }
};

const sendImage = (): void => {
    messages.value.push({
        id: generateUniqueId(),
        role: 'bot',
        text: 'Xin lỗi, chức năng gửi ảnh hiện chưa được hỗ trợ trong bản demo này.',
        bookSuggestions: extractFallbackSuggestions(null).map(s => ({ display: s, fullText: s })),
        suggestions: [], // Ensure suggestions is an empty array
    });
};

const useSuggestion = (suggestText: string | undefined): void => {
    if (typeof suggestText === 'string') {
        prompt.value = suggestText;
        handleSendMessage(); // Tự động gửi tin nhắn khi chọn gợi ý
    }
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