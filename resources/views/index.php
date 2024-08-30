<div class="flex w-full h-full">
    <div id="people" class="w-1/4 rounded-md fixed h-screen p-2">
        <div class="relative bg-gradient-to-b from-slate-950/90 to-blue-950/90 backdrop-blur-lg border border-gray-300 border-opacity-30 overflow-y-scroll h-full rounded-md ">
            <div class="p-5">
                <div class="bg-slate-300/20 px-3  p-2 rounded-lg backdrop-blur-xl shadow-sm w-full">
                    <p class="text-cyan-300">Welcome!</p>
                    <h1 class="text-slate-100 text-2xl pb-2.5 ">@<?= htmlspecialchars($data['username']); ?></h1>
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

    <?php if (isset($messages)): ?>
        <div class="w-3/4 right-0 bg-transparent absolute h-screen overflow-scroll">
            <div class="bg-slate-900/70 backdrop-blur-lg rounded-md mt-1 sticky top-0 flex items-center mb-2 p-4">
                <h1 class="text-slate-200">@<?= htmlspecialchars($theirusername ?? ''); ?></h1>
            </div>
            <div id="messages" class="bg-blue-900/30 backdrop-blur-lg  rounded p-4 py-24 overflow-y-auto" style=" height: calc(100vh - 80px); ">
                <?php foreach ($messages as $msg): ?>
                    <?php if ($msg['from'] === $userid): ?>
                        <div class="flex justify-end mb-2">
                            <div class="bg-blue-500/20 backdrop-blur-lg text-white p-3 rounded-l-lg rounded-br-lg max-w-[320px]">
                                <p class="text-sm"><?= htmlspecialchars($msg['message']); ?></p>
                                <span class="text-xs text-gray-200"><?= htmlspecialchars(MessageTime($msg['created_at'])); ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex justify-start mb-2">
                            <div class="bg-gray-100/50 p-3 rounded-r-lg rounded-bl-lg max-w-[320px]">
                                <p class="text-sm text-gray-900"><?= htmlspecialchars($msg['message']); ?></p>
                                <span class="text-xs text-gray-500"><?= htmlspecialchars(
                                                                        MessageTime($msg['created_at'])
                                                                    ); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="fixed w-3/4 bottom-0 flex items-center mb-2 bg-slate-900/70 backdrop-blur rounded-md p-4">
                <input type="text" id="msg" placeholder="Type your message..." class="border-0 bg-slate-300/10 basis-full text-white rounded-md py-2 px-4 mr-2 flex-grow">
                <button id="sendMessageBtn" class="bg-green-500 hover:bg-green-700 py-2 px-2 rounded-full text-white font-bold">
                    <svg class="w-6 h-6 rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 18-7 3 7-18 7 18-7-3Zm0 0v-5" />
                    </svg>
                </button>
            </div>
        </div>
    <?php else: ?>
        <div class="w-3/4 right-0 absolute h-5/6 flex flex-col justify-center items-center bg-blue-900/30 backdrop-blur-lg  rounded-md my-10">
            <div>
                <h1 class="text-4xl text-cyan-300 font-semibold text-center mb-5">ChatApp</h1>
                <p class="text-center">Search For User</p>
                <form action="/search" class="my-5">
                    <input type="text" class="bg-slate-300/10 w-full h-10 border-0 text-white rounded-md text-center" name="username" placeholder="Username......">
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
<div id="alert" class="z-50 fixed top-0 right-0 pe-2 pt-2"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script>
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
            console.error("WebSocket error: " + error.message);
            showAlert("No connection");
        };

        document.getElementById('sendMessageBtn').addEventListener('click', sendMessage);
        document.getElementById('msg').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

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
                            <div class="bg-gray-100/50 p-3 rounded-r-lg rounded-bl-lg max-w-[320px]">
                            <p class="text-sm text-gray-900">${data.message}</p>
                            <span class="text-xs text-gray-500">${data.created_at}</span>
                        </div>
                    </div>`;
                    const messagesContainer = document.getElementById('messages');
                    messagesContainer.innerHTML += html;
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                } else if (data.from !== parseInt(getThey()) && data.to == parseInt(getThey())) {
                    const content = `  <div class="flex justify-end mb-2">
                                                       <div class="bg-blue-500/20 backdrop-blur-lg text-white p-3 rounded-l-lg rounded-br-lg max-w-[320px]">

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