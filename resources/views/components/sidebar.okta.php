<div id="sidebar">
    <div id="people" class="fixed transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out hidden md:block w-full md:w-1/4 lg:w-1/5 h-screen p-2 px-1" style="z-index: 49;">
        <div class="relative bg-gradient-to-b from-slate-950/80 to-blue-950/90 backdrop-blur-lg border border-gray-300 border-opacity-30  h-full rounded-md">
            <div class="flex justify-end md:hidden p-5">
                <button onclick="closeSidebar()" class="text-white"><svg class="w-6 h-6  text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="px-2.5 py-5">
                <div class="bg-indigo-100/20 px-3 p-2 shadow-lg rounded-lg backdrop-blur-xl  w-full">
                    <p class="text-cyan-300">Welcome!</p>
                    <div class="flex w-full justify-between items-center">

                        <h1 class="text-slate-100 text-2xl  ">@<?= $data["username"]; ?></h1>
                        <button onclick="Logout()" class="hover:bg-slate-100/20 p-1 rounded-md">
                            <svg height="14px" width="14px" version="1.1" style="opacity: .7;" fill="#f87171" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 30.143 30.143" xml:space="preserve">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <g>
                                        <path d="M20.034,2.357v3.824c3.482,1.798,5.869,5.427,5.869,9.619c0,5.98-4.848,10.83-10.828,10.83 c-5.982,0-10.832-4.85-10.832-10.83c0-3.844,2.012-7.215,5.029-9.136V2.689C4.245,4.918,0.731,9.945,0.731,15.801 c0,7.921,6.42,14.342,14.34,14.342c7.924,0,14.342-6.421,14.342-14.342C29.412,9.624,25.501,4.379,20.034,2.357z"></path>
                                        <path d="M14.795,17.652c1.576,0,1.736-0.931,1.736-2.076V2.08c0-1.148-0.16-2.08-1.736-2.08 c-1.57,0-1.732,0.932-1.732,2.08v13.496C13.062,16.722,13.225,17.652,14.795,17.652z"></path>
                                    </g>
                                </g>
                            </svg>
                        </button>
                    </div>
                    <a href="/" class="text-xs text-teal-200 px-2 rounded-md bg-slate-100/10">to /</a>
                </div>
            </div>
            <div class="px-5 mb-3">
                <h1 class="text-xl font-semibold text-cyan-200">Chats</h1>
            </div>
            <ul class=" userlist max-w-md shadow-lg divide-y p-5 divide-gray-200 overflow-y-scroll relative bg-emerald-300/10 mx-2 rounded-md" id="userlist">

                <?php foreach ($users as $user): ?>
                    <a onclick="nav('/messages?with=<?= $user['id'] ?>')" class="pb-3 sm:pb-4" id="user<?= $user['id'] ?>">
                        <div class="flex my-1 items-center space-x-4 p-2 px-4 cursor-pointer hover:shadow hover:bg-slate-200/10 hover:rounded-md border-cyan-200/30 border-b-2" style="border-bottom-width: 1px;">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-200 truncate"><?= htmlspecialchars($user['username']); ?></p>
                                <p class="text-sm text-gray-500 truncate">#<?= $user['id']; ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>