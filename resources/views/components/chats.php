    <div class="w-full md:w-3/4 md:right-0 lg:w-4/5 bg-transparent absolute h-screen overflow-scroll" >
        <div class="bg-slate-900/70 backdrop-blur-lg rounded-md mt-1 shadow-lg sticky top-0 justify-between flex items-center mb-2 p-4">
            <button class="md:hidden text-cyan-300" onclick="showSidebar()"><svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4" />
                </svg>
            </button>
            <h1 class="text-slate-200 bg-slate-950/50 px-5 shadow-lg p-1.5 rounded-md" id="theirusername">@<?= htmlspecialchars($theirusername ?? ''); ?></h1>
        </div>
        <div id="messages" class="bg-blue-900/20 backdrop-blur-lg rounded p-4 py-24 overflow-y-auto" style="height: calc(100vh - 80px);">
            <?php foreach ($messages as $msg): ?>
                <?php if ($msg['from'] === $userid): ?>
                    <div class="flex justify-end mb-2">
                        <div class="bg-blue-500/20 backdrop-blur-lg text-white p-3 rounded-l-lg rounded-br-lg max-w-[320px] text-wrap overflow-hidden breaks-all" style="word-break: break-all;
">
                            <p class="text-sm"><?= htmlspecialchars($msg['message']); ?></p>
                            <span class="text-xs text-gray-200"><?= htmlspecialchars(MessageTime($msg['created_at'])); ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex justify-start mb-2">
                        <div class="bg-gray-100/50 p-3 rounded-r-lg rounded-bl-lg max-w-[320px] overflow-hidden breaks-all" style="word-break: break-all;
">
                            <p class="text-sm text-gray-900"><?= htmlspecialchars($msg['message']); ?></p>
                            <span class="text-xs text-gray-500"><?= htmlspecialchars(MessageTime($msg['created_at'])); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="fixed w-full md:w-3/4 lg:w-4/5 bottom-0 flex items-center mb-2 bg-slate-900/70 backdrop-blur rounded-md p-4">
            <input type="text" id="msg" placeholder="Type your message..." class="border-0 bg-slate-300/10 basis-full text-white rounded-md py-2 px-4 mr-2 flex-grow">
            <button id="sendMessageBtn" class="bg-green-500 hover:bg-green-700 py-2 px-2 rounded-full text-white font-bold">
                <svg class="w-6 h-6 rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 18-7 3 7-18 7 18-7-3Zm0 0v-5" />
                </svg>
            </button>
        </div>
    </div>