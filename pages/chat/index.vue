<template>
  <div class="flex flex-col h-[calc(100vh-4rem)] bg-muted/20 dark:bg-muted/10 pt-0 sm:pt-4 pb-0 sm:pb-4">
    <div class="container mx-auto flex flex-grow items-stretch h-full"> <!-- Modified this line -->
      <Card class="flex-grow flex flex-col shadow-none border-0 sm:border rounded-none sm:rounded-lg w-full">
        <CardHeader class="flex flex-row items-center justify-between p-3 sm:p-4 border-b">
          <div class="flex items-center gap-2 sm:gap-2.5">
            <IconMessageCircle class="w-6 h-6 sm:w-7 sm:h-7 text-primary cursor-pointer hover:opacity-80"
                @click="showInfo = true" title="Chat Bot Information" />
            <CardTitle class="text-base sm:text-lg font-semibold">Chat with MyAppBot</CardTitle>
          </div>
          <Button variant="outline" size="sm" @click="startNewChat" title="Start New Chat">
            <IconPlusCircle class="mr-1.5 h-4 w-4" />
            New Chat
          </Button>
        </CardHeader>

        <CardContent ref="chatBodyRef" class="flex-1 overflow-y-auto p-3 sm:p-4 space-y-3 bg-background">
          <TransitionGroup name="message-item" tag="div">
              <div v-for="msg in messages" :key="msg.id" :class="[
                  'rounded-lg py-2 px-3 shadow-sm w-max max-w-[85%] sm:max-w-[80%] text-sm sm:text-base leading-relaxed mb-3 break-words',
                  msg.role === 'user'
                      ? 'bg-primary text-primary-foreground ml-auto rounded-br-none'
                      : 'bg-muted text-muted-foreground mr-auto rounded-bl-none border',
              ]">
                  <div class="whitespace-pre-wrap">{{ msg.text }}</div>
                  <div class="whitespace-pre-wrap" v-if="msg.noi_dung">{{ msg.noi_dung }}</div>

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

                  <div v-if="msg.role === 'bot' && msg.bookSuggestions && msg.bookSuggestions.length > 0"
                      class="mt-2 flex flex-wrap gap-1.5">
                      <Button v-for="(suggest, sidx) in msg.bookSuggestions" :key="`bs-${sidx}`"
                          variant="outline" size="sm"
                          class="text-xs h-auto py-1 px-2.5"
                          @click="useSuggestion(typeof suggest === 'string' ? suggest : suggest.fullText || suggest.display)">
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
          <Transition name="message-item">
              <div v-if="isTyping" key="typing-indicator"
                  class="rounded-lg py-2 px-3.5 shadow-sm w-max max-w-[85%] sm:max-w-[80%] text-sm sm:text-base leading-relaxed mb-3 bg-muted text-muted-foreground mr-auto rounded-bl-none border flex items-center space-x-1.5">
                  <div class="typing-dot bg-primary"></div>
                  <div class="typing-dot bg-primary" style="animation-delay: 0.2s;"></div>
                  <div class="typing-dot bg-primary" style="animation-delay: 0.4s;"></div>
              </div>
          </Transition>
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

        <CardFooter class="p-2 sm:p-3 border-t flex flex-col items-center gap-1.5 sm:gap-2 bg-background">
          <p v-if="limitReachedUnauth" class="text-xs text-destructive text-center w-full mb-1">
            Message limit reached. Start a new chat or log in.
          </p>
          <div class="flex w-full items-center gap-1.5 sm:gap-2">
              <Input ref="promptInputRef" v-model="prompt" :disabled="isTyping || limitReachedUnauth"
                  class="flex-1 text-sm sm:text-base py-2 px-3 h-10 sm:h-11"
                  placeholder="Nhập tin nhắn..." aria-label="Chat message input" @keyup.enter="handleSendMessage" />
              <Button type="button" variant="outline" size="icon"
                  class="h-10 w-10 sm:h-11 sm:w-11 text-muted-foreground hover:text-foreground disabled:opacity-50"
                  @click="sendImage" title="Send image (not supported)" :disabled="isTyping || limitReachedUnauth" aria-label="Send image">
                  <IconImage class="w-5 h-5" />
              </Button>
              <Button type="submit" :disabled="!prompt.trim() || isTyping || limitReachedUnauth"
                  class="h-10 w-16 sm:h-11 sm:w-20 disabled:opacity-60"
                  title="Send message" aria-label="Send message" @click="handleSendMessage">
                  <IconSend class="w-5 h-5" />
              </Button>
          </div>
        </CardFooter>
      </Card>
    </div>

    <Transition name="modal-fade">
      <div v-if="showInfo"
          class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[1001] p-4"
          @click.self="showInfo = false" aria-modal="true" role="dialog">
          <Card class="w-full max-w-md bg-card text-card-foreground transform transition-all duration-300 ease-out">
              <CardHeader class="items-center text-center pb-4">
                  <IconMessageCircle class="w-12 h-12 text-primary mb-2" />
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
import { ref, nextTick, watch, onMounted } from 'vue';
import type { Ref } from 'vue';

interface MessageBook {
  tieu_de: string; tac_gia: string; the_loai: string;
  mo_ta_ngan_gon: string; ly_do_phu_hop: string; suggestions?: string;
}
interface MessageSuggestion { display: string; fullText: string; }
type SuggestionItem = string | { display: string; fullText?: string };
interface Message {
  id: string; role: 'user' | 'bot'; text?: string; noi_dung?: string;
  books?: MessageBook[]; bookSuggestions?: MessageSuggestion[]; suggestions?: SuggestionItem[];
}

const chatHistoryKey = 'chatHistory_myapplogo_v1';
const REQUEST_LIMIT_UNAUTH_KEY = 'chatRequestCount_unauth_v1';
const MAX_REQUESTS_UNAUTH = 20;

const requestCountUnauth = ref(0);
const limitReachedUnauth = ref(false);

const prompt: Ref<string> = ref('');
const promptInputRef = ref<HTMLInputElement | null>(null);
const messages: Ref<Message[]> = ref([]);
const showInfo: Ref<boolean> = ref(false);
const chatBodyRef: Ref<HTMLElement | null> = ref(null);
const isTyping: Ref<boolean> = ref(false);

const generateUniqueId = (): string => `msg_${Date.now()}_${Math.random().toString(36).substring(2, 9)}`;

function extractFallbackSuggestions(replyText?: string | null): string[] {
    if (!replyText) { return [ 'Bạn muốn tìm sách thuộc thể loại nào?', 'Bạn quan tâm đến tác giả hoặc chủ đề nào?', 'Bạn cần gợi ý sách phù hợp với lứa tuổi hoặc mục đích nào?' ]; }
    const lower: string = replyText.toLowerCase();
    if (lower.includes('xin chào') || lower.includes('chào bạn')) { return [ 'Tôi muốn được gợi ý sách hay', 'Bạn có thể giới thiệu sách theo sở thích không?', 'Có sách nào phù hợp với tôi không?' ]; }
    if (lower.includes('thể loại')) { return [ 'Gợi ý sách thể loại trinh thám', 'Tôi thích sách khoa học viễn tưởng', 'Có sách nào về phát triển bản thân không?' ]; }
    if (lower.includes('tác giả')) { return [ 'Giới thiệu sách của tác giả nổi tiếng', 'Tôi muốn tìm sách của Nguyễn Nhật Ánh', 'Có sách nào của Dan Brown không?' ]; }
    return [ 'Bạn thích thể loại sách nào?', 'Bạn muốn tìm sách cho mục đích gì?', 'Bạn có muốn gợi ý sách bán chạy không?' ];
}

const scrollToBottom = async (): Promise<void> => {
    await nextTick();
    if (chatBodyRef.value) {
        chatBodyRef.value.scrollTop = chatBodyRef.value.scrollHeight;
    }
};

watch(messages, (newMessages) => {
    if (typeof window !== 'undefined') {
        localStorage.setItem(chatHistoryKey, JSON.stringify(newMessages));
    }
    nextTick(() => scrollToBottom());
}, { deep: true });

const focusPromptInput = async () => {
  await nextTick();
  if (promptInputRef.value) {
    try {
        if (typeof (promptInputRef.value as any).focus === 'function') {
             (promptInputRef.value as any).focus();
        }
        else if (promptInputRef.value instanceof HTMLInputElement) {
            promptInputRef.value.focus();
        }
        else if ((promptInputRef.value as any).$el && typeof (promptInputRef.value as any).$el.focus === 'function') {
             (promptInputRef.value as any).$el.focus();
        }
        else if ((promptInputRef.value as any).$el && typeof (promptInputRef.value as any).$el.querySelector === 'function') {
            const inputInside = (promptInputRef.value as any).$el.querySelector('input');
            if (inputInside) inputInside.focus();
        } else {
            console.warn("Could not determine how to focus the prompt input ref.");
        }
    } catch (e) {
        console.warn("Error attempting to focus prompt input:", e);
    }
  }
};

const handleSendMessage = async (): Promise<void> => {
    if (limitReachedUnauth.value) {
      messages.value.push({
        id: generateUniqueId(), role: 'bot',
        text: "You have reached the message limit for this session. Please start a new chat or log in for more.",
      });
      scrollToBottom(); return;
    }
    if (!prompt.value.trim() || isTyping.value) return;

    const userMessageText: string = prompt.value;
    messages.value.push({ id: generateUniqueId(), role: 'user', text: userMessageText });
    prompt.value = '';
    focusPromptInput();
    await scrollToBottom();
    isTyping.value = true;
    try {
        interface ApiResponse {
          reply_text?: string; noi_dung?: string; goi_y_cau_hoi?: SuggestionItem[];
          goi_y_sach?: MessageBook[]; error_details?: string;
        }
        const responseData = await $fetch<ApiResponse>('/api/chat', {
            method: 'POST', body: { prompt: userMessageText },
        });
        requestCountUnauth.value++;
        if (typeof window !== 'undefined') localStorage.setItem(REQUEST_LIMIT_UNAUTH_KEY, requestCountUnauth.value.toString());
        if (requestCountUnauth.value >= MAX_REQUESTS_UNAUTH) limitReachedUnauth.value = true;

        let botReplyText: string = responseData.reply_text || "Xin lỗi, tôi chưa hiểu ý bạn.";
        let suggestionsForBot: SuggestionItem[] = [];
        let bookSuggestionsForBot: MessageSuggestion[] = [];
        let booksForBot: MessageBook[] = [];
        let noi_dung_bot: string = responseData?.noi_dung || "";

        if (responseData.goi_y_cau_hoi && responseData.goi_y_cau_hoi.length > 0) suggestionsForBot = responseData.goi_y_cau_hoi;
        if (responseData.goi_y_sach && responseData.goi_y_sach.length > 0) booksForBot = responseData.goi_y_sach.map(sach => ({ ...sach, suggestions: `Hãy cho tôi biết thêm về sách ${sach.tieu_de}` }));
        else if (responseData.reply_text) bookSuggestionsForBot = extractFallbackSuggestions(responseData.reply_text).map(s => ({ display: s, fullText: s }));
        else {
            botReplyText = responseData.error_details || "Có lỗi xảy ra, vui lòng thử lại sau.";
            bookSuggestionsForBot = extractFallbackSuggestions(null).map(s => ({ display: s, fullText: s }));
        }
        if (responseData.error_details) botReplyText = `${botReplyText} (Lỗi: ${responseData.error_details})`;

        const botMessage: Message = {
            id: generateUniqueId(), role: 'bot', text: botReplyText,
            bookSuggestions: bookSuggestionsForBot, suggestions: suggestionsForBot,
            books: booksForBot, noi_dung: noi_dung_bot,
        };
        messages.value.push(botMessage);
        if (limitReachedUnauth.value && messages.value[messages.value.length -1].id === botMessage.id) {
             messages.value.push({
                 id: generateUniqueId(), role: 'bot',
                 text: "You have now reached your message limit for this session. To continue, please start a new chat or log in.",
               });
        }
    } catch (error: any) {
        console.error("Lỗi khi gọi API /api/chat:", error);
        let errorMessage = 'Xin lỗi, đã có lỗi xảy ra khi kết nối với AI.';
        if (error.data?.message) errorMessage = error.data.message;
        else if (error.statusMessage) errorMessage = error.statusMessage;
        else if (error.message) errorMessage = error.message;
        messages.value.push({
            id: generateUniqueId(), role: 'bot', text: errorMessage,
            bookSuggestions: extractFallbackSuggestions(null).map(s => ({ display: s, fullText: s })),
            suggestions: [],
        });
    } finally {
        isTyping.value = false; await scrollToBottom();
    }
};

const sendImage = (): void => {
    messages.value.push({
        id: generateUniqueId(), role: 'bot', text: 'Xin lỗi, chức năng gửi ảnh hiện chưa được hỗ trợ trong bản demo này.',
        bookSuggestions: extractFallbackSuggestions(null).map(s => ({ display: s, fullText: s })),
        suggestions: [],
    });
};

const useSuggestion = (suggestText: string | undefined): void => {
    if (typeof suggestText === 'string') {
        prompt.value = suggestText;
        handleSendMessage();
    }
};

const startNewChat = () => {
  messages.value = [];
  if (typeof window !== 'undefined') {
    localStorage.removeItem(chatHistoryKey);
    localStorage.removeItem(REQUEST_LIMIT_UNAUTH_KEY);
  }
  prompt.value = '';
  isTyping.value = false;
  requestCountUnauth.value = 0;
  limitReachedUnauth.value = false;
  nextTick(() => {
    scrollToBottom();
    focusPromptInput();
  });
};

onMounted(async () => {
  if (typeof window !== 'undefined') {
    const storedHistory = localStorage.getItem(chatHistoryKey);
    if (storedHistory) {
      try {
        const parsedHistory = JSON.parse(storedHistory) as Message[];
        if (Array.isArray(parsedHistory) && parsedHistory.every(msg => msg.id && msg.role)) {
            messages.value = parsedHistory;
        } else {
            console.warn("Stored chat history is invalid. Starting fresh.");
            localStorage.removeItem(chatHistoryKey);
        }
      } catch (e) {
        console.error("Failed to parse chat history:", e);
        localStorage.removeItem(chatHistoryKey);
      }
    }
    const storedRequestCount = localStorage.getItem(REQUEST_LIMIT_UNAUTH_KEY);
    if (storedRequestCount) requestCountUnauth.value = parseInt(storedRequestCount, 10) || 0;
    if (requestCountUnauth.value >= MAX_REQUESTS_UNAUTH) limitReachedUnauth.value = true;
  }
  await nextTick();
  scrollToBottom();
  focusPromptInput();
});

</script>

<style>
.message-item-enter-active { transition: all 0.4s ease-out; }
.message-item-leave-active { transition: all 0.2s ease-in; }
.message-item-enter-from { opacity: 0; transform: translateY(20px) scale(0.95); }
.message-item-leave-to { opacity: 0; transform: translateX(30px) scale(0.9); }
.message-item-move { transition: transform 0.4s ease; }

.typing-dot {
    width: 8px; height: 8px; background-color: hsl(var(--primary));
    border-radius: 50%; animation: typing-blink 1.4s infinite both;
}
@keyframes typing-blink { 0% { opacity: 0.2; } 20% { opacity: 1; } 100% { opacity: 0.2; } }

.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.3s ease; }
.modal-fade-enter-active > div, .modal-fade-leave-active > div { transition: transform 0.3s ease-out; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
.modal-fade-enter-from > div, .modal-fade-leave-to > div { transform: scale(0.90) translateY(15px); }

.overflow-y-auto::-webkit-scrollbar { width: 6px; }
.overflow-y-auto::-webkit-scrollbar-track { background: hsl(var(--background)); border-radius: 10px; }
.overflow-y-auto::-webkit-scrollbar-thumb { background: hsl(var(--muted-foreground)); border-radius: 10px; }
.overflow-y-auto::-webkit-scrollbar-thumb:hover { background: hsl(var(--primary)); }
.overflow-y-auto { scrollbar-width: thin; scrollbar-color: hsl(var(--muted-foreground)) hsl(var(--background)); }
</style>
