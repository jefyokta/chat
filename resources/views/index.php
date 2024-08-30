<div class="flex w-full h-full">
    <!-- Sidebar -->
    <div id="people" class="fixed transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out hidden md:block w-full md:w-1/4 lg:w-1/5 h-screen p-2" style="z-index: 49;">
        <div class="relative bg-gradient-to-b from-slate-950/90 to-blue-950/90 backdrop-blur-lg border border-gray-300 border-opacity-30 overflow-y-scroll h-full rounded-md">
            <div class="flex justify-end md:hidden p-5">
                <button onclick="closeSidebar()" class="text-white"><svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="p-5">
                <div class="bg-slate-300/20 px-3 p-2 rounded-lg backdrop-blur-xl shadow-sm w-full">
                    <p class="text-cyan-300">Welcome!</p>
                    <div class="flex w-full justify-between items-center">

                        <h1 class="text-slate-100 text-2xl  ">@<?= htmlspecialchars($data['username']); ?></h1>
                        <button onclick="Logout()">
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
            <div class="px-5">
                <h1 class="text-xl font-semibold text-cyan-200">Chats</h1>
            </div>
            <ul class="max-w-md divide-y p-5 divide-gray-200 overflow-y-scroll relative">
                <?php foreach ($users as $user): ?>
                    <a href="/messages?with=<?= htmlspecialchars($user['id']) ?>" class="pb-3 sm:pb-4" id="user<?= $user['id'] ?>">
                        <div class="flex items-center space-x-4 p-2 px-4 hover:bg-slate-200/10 rounded-md">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-200 truncate"><?= htmlspecialchars($user['username']); ?></p>
                                <p class="text-sm text-gray-500 truncate">email@flowbite.com</p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Main content -->
    <?php if (isset($messages)): ?>
        <div class="w-full md:w-3/4 md:right-0 lg:w-4/5 bg-transparent absolute h-screen overflow-scroll">
            <div class="bg-slate-900/70 backdrop-blur-lg rounded-md mt-1 sticky top-0 justify-between flex items-center mb-2 p-4">
                <button class="md:hidden text-cyan-300" onclick="showSidebar()"><svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4" />
                    </svg>
                </button>
                <h1 class="text-slate-200">@<?= htmlspecialchars($theirusername ?? ''); ?></h1>
            </div>
            <div id="messages" class="bg-blue-900/30 backdrop-blur-lg rounded p-4 py-24 overflow-y-auto" style="height: calc(100vh - 80px);">
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
    <?php else: ?>
        <div class="w-full md:w-3/4 md:right-0 lg:w-4/5 absolute h-5/6 flex flex-col justify-center items-center bg-blue-900/30 backdrop-blur-lg rounded-md my-10">
            <div>
                <h1 class="text-4xl text-cyan-300 font-semibold text-center mb-5">

                    ChatApp</h1>
                <button class="md:hidden text-cyan-100 text-center w-full p-2 bg-cyan-200/20 rounded-md mb-5" onclick="showSidebar()">Open Chat
                </button>
                <p class="text-center">Search For User</p>
                <form action="/search" class="my-5">
                    <input type="text" class="bg-slate-300/10 w-full h-10 border-0 text-white rounded-md text-center" name="username" placeholder="Username......">
                </form>
                <?php if (isset($search)) : ?>
                    <div class="bg-cyan-700/10 rounded-md py-5 px-2 overflow-x-scroll max-h-60">
                        <?php foreach ($search as $s): ?>
                            <?php if ($s['id'] !== $userid) { ?>
                                <div class="flex justify-between mb-1 bg-slate-100/10 p-2 rounded-md">
                                    <h1 class="text-cyan-200">
                                        <?= $s['username']; ?>
                                    </h1>
                                    <a href="/messages?with=<?= $s['id'] ?>">
                                        <svg class="w-6 h-6 text-gray-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M3 5.983C3 4.888 3.895 4 5 4h14c1.105 0 2 .888 2 1.983v8.923a1.992 1.992 0 0 1-2 1.983h-6.6l-2.867 2.7c-.955.899-2.533.228-2.533-1.08v-1.62H5c-1.105 0-2-.888-2-1.983V5.983Zm5.706 3.809a1 1 0 1 0-1.412 1.417 1 1 0 1 0 1.412-1.417Zm2.585.002a1 1 0 1 1 .003 1.414 1 1 0 0 1-.003-1.414Zm5.415-.002a1 1 0 1 0-1.412 1.417 1 1 0 1 0 1.412-1.417Z" clip-rule="evenodd" />
                                        </svg>

                                    </a>
                                </div>
                            <?php  }; ?>


                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<div id="alert" class="z-50 fixed top-0 right-0 pe-2 pt-2"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script>
    const Logout = async () => {

        confirm("mau logout?")

        await fetch(`/logout`, {
            method: "delete"
        })
        location.href = "/login"

    }
    const showSidebar = () => {
        const sidebar = document.getElementById('people');
        sidebar.classList.remove('hidden');
        setTimeout(() => {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
        }, 100);
    };

    const closeSidebar = () => {
        const sidebar = document.getElementById('people');
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        setTimeout(() => {
            sidebar.classList.add('hidden');
        }, 300);


    }
    const animasi = (id) => {
        const test = document.getElementById(id)
        const parent = test.parentNode
        test.classList.add("relative")
        test.classList.add("top-0")
        parent.insertBefore(test, parent.firstChild)
        anime({
            targets: test,
            translateY: [-50, 0],
            scale: [0.8, 1],
            opacity: [0, 1],
            duration: 1000,
            easing: 'easeOutElastic',
            complete: () => {
                test.style.zIndex = '';
            }
        });
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const socket = new WebSocket('ws://<?= config('ws.host') ?>:<?= config("ws.port") ?>');
        const token = getCookie('X-ChatAppAccessToken') || null;



        const messagesContainer = document.getElementById('messages');
        if (messagesContainer) {

            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        socket.onopen = async function() {
            // showAlert("You Are Connected now",false);
            if (token) {
                await GetToken();
                socket.send(JSON.stringify({
                    token: token,
                    data: {
                        action: 'auth'
                    }
                }));
            }
        };

        socket.onmessage = function(event) {
            const messageData = JSON.parse(event.data);
            console.log(messageData);
            handleMessage(messageData);
        };

        socket.onclose = function() {
            showAlert('WebSocket Closed');
        };

        socket.onerror = function(error) {
            console.error(error);
            showAlert("No connection");
        };
        const input = document.getElementById('sendMessageBtn');
        if (input) {
            document.getElementById('sendMessageBtn').addEventListener('click', sendMessage);
            document.getElementById('msg').addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        }

        async function sendMessage() {
            const message = document.getElementById('msg').value.trim();
            if (message) {
                await GetToken();
                try {
                    // Pastikan socket sudah terbuka
                    if (socket.readyState === WebSocket.OPEN) {
                        socket.send(JSON.stringify({
                            token: getCookie('X-ChatAppAccessToken'),
                            data: {
                                action: 'message',
                                to: getThey(),
                                message: message,
                            }
                        }));

                        // Bersihkan input pesan
                        document.getElementById('msg').value = '';

                        // Update UI: Pindahkan elemen pesan baru ke atas
                        let box = document.getElementById("user" + getThey());
                        if (box) {
                            const container = box.parentNode;
                            if (container) {
                                container.insertBefore(box, container.firstChild);
                            }
                        }
                    } else {
                        showAlert("Not Connection Available.", true);
                    }
                } catch (e) {
                    console.error(e);
                    showAlert("Lost Connection...", true);
                }
            }
        }


        function handleMessage(messages) {
            const {
                type,
                data,
                message
            } = messages;

            if (type === 'message') {
                if (data.from == parseInt(getThey())) {
                    const html = `
                    <div class="flex justify-start mb-2">
                            <div class="bg-gray-100/50 p-3 rounded-r-lg rounded-bl-lg max-w-[320px] overflow-hidden breaks-all" style="word-break: break-all;
">
                            <p class="text-sm text-gray-900">${data.message}</p>
                            <span class="text-xs text-gray-500">${data.created_at}</span>
                        </div>
                    </div>`;
                    const messagesContainer = document.getElementById('messages');
                    messagesContainer.innerHTML += html;
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                } else if (data.from !== parseInt(getThey()) && data.to == parseInt(getThey())) {
                    const content = `  <div class="flex justify-end mb-2">
                                                       <div class="bg-blue-500/20 backdrop-blur-lg text-white p-3 rounded-l-lg rounded-br-lg max-w-[320px] overflow-hidden breaks-all" style="word-break: break-all;
">

                                <p class="text-sm">${data.message}</p>
                                <span class="text-xs text-gray-200">${data.created_at}</span>
                            </div>
                        </div>`
                    const messagesContainer = document.getElementById('messages');
                    messagesContainer.innerHTML += content;
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;

                }
            } else if (type === 'new') {
                const myid = <?= $userid ?>;
                const fromUserId = data.from;
                const toUserId = data.to;

                if (fromUserId !== myid || toUserId !== myid) {
                    const box = document.getElementById(fromUserId);

                    if (box) {
                        const container = box.parentNode;

                        if (container) {
                            animasi(fromUserId)
                            showAlert("Hei There a new Message!", false)

                        } else {
                            console.error("Container tidak ditemukan untuk elemen dengan id" + fromUserId + "'");
                        }
                    } else {
                        console.error("Elemen dengan id " + fromUserId + "' tidak ditemukan");
                    }
                }
            }



        }


        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }

        function showAlert(message, error = true) {
            const toastId = `toast-${Date.now()}`;

            svg = !error ? ` <svg class="w-5 h-5 text-blue-600 dark:text-blue-500 rotate-45" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 17 8 2L9 1 1 19l8-2Zm0 0V9"/>
    </svg>` : ` <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
                </svg>`
            const toastHtml = `
        <div id="${toastId}" class="toast flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400" role="alert">
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${error ?"text-red-500 bg-red-100 " :''}rounded-lg">
        ${svg}
            </div>
            <div class="ms-3 text-sm font-normal">${message}</div>
            <button class="toast-close ms-auto" aria-label="Close">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 14 14">
                    <path d="M7 0a7 7 0 1 0 0 14A7 7 0 0 0 7 0Zm2.5 9.5a.5.5 0 0 1-.707-.707L7 7.707 5.207 5.5a.5.5 0 0 1 .707-.707L7 6.293l1.793-1.793a.5.5 0 0 1 .707.707L7.707 7.707l1.793 1.793a.5.5 0 0 1 0 .707Z"/>
                </svg>
            </button>
        </div>`;

            const alertContainer = document.getElementById('alert');
            alertContainer.innerHTML += toastHtml;

            const toastElement = document.getElementById(toastId);

            const closeButton = toastElement.querySelector('.toast-close');
            closeButton.addEventListener('click', () => {
                toastElement.classList.add('hide');
                setTimeout(() => {
                    const element = document.getElementById(toastId);
                    if (element) {
                        element.remove();
                    }
                }, 500);
            });

            setTimeout(() => {
                const element = document.getElementById(toastId);
                if (element && !element.classList.contains('hide')) {
                    element.classList.add('hide');
                    setTimeout(() => {
                        if (document.getElementById(toastId)) {
                            document.getElementById(toastId).remove();
                        }
                    }, 500);
                }
            }, 2000);
        }





        const getThey = () => (new URLSearchParams(window.location.search)).get('with') || '';


        async function GetToken() {
            if (!token) {
                token = await fetch('/api/auth/token').then(res => res.json());
            }
        }
    });
</script>