<div class="flex w-full h-full">
    <!-- Sidebar -->
    <?php require __DIR__ . "/components/sidebar.okta.php"; ?>


    <!-- Main content -->
    <?php if (isset($messages)): ?>
        <?php require __DIR__ . "/components/chats.php"; ?>
    <?php else: ?>
        <?php require __DIR__ . "/components/welcome.php"; ?>
    <?php endif; ?>
</div>

<div id="alert" class="z-50 fixed top-0 right-0 pe-2 pt-2"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script>
    const nav = function(url) {
        history.pushState(null, "", url);
        fetch(`/api${url}`)
            .then((r) => r.json())
            .then((res) => {
                const msgcon = document.getElementById("messages");
                if (!msgcon) {
                    location.href = url;
                }
                const {
                    messages,
                    user
                } = res;
                document.getElementById("theirusername").innerHTML = "@" + user.username;
                const my = <?= $userid ?>;
                msgcon.innerHTML = "";
                messages.map((item) => {
                    console.log(getThey())
                    if (item.from == my) {
                        msgcon.innerHTML += ` <div class="flex justify-end mb-2"><div class="bg-blue-500/20 backdrop-blur-lg text-white p-3 rounded-l-lg rounded-br-lg max-w-[320px] text-wrap overflow-hidden breaks-all" style="word-break: break-all;"><p class="text-sm">${item.message}</p><span class="text-xs text-gray-200">${formatDateTime(item.created_at)}</span></div></div>`;
                    } else {
                        msgcon.innerHTML += ` <div class="flex justify-start mb-2"><div class="bg-gray-100/50 p-3 rounded-r-lg rounded-bl-lg max-w-[320px] overflow-hidden breaks-all" style="word-break: break-all;"><p class="text-sm text-gray-900">${item.message}</p><span class="text-xs text-gray-500">${formatDateTime(item.created_at)}</span></div></div>`;
                    }
                });
                msgcon.scrollTop = msgcon.scrollHeight

            });
    };

    const formatDateTime = (targetDate) => {
        const now = new Date();

        targetDate = new Date(targetDate);

        const timeDifference = now - targetDate;

        const dayDifference = timeDifference / (1000 * 60 * 60 * 24);

        const hours = targetDate.getHours().toString().padStart(2, '0');
        const minutes = targetDate.getMinutes().toString().padStart(2, '0');
        const time = `${hours}:${minutes}`;

        if (now.toDateString() === targetDate.toDateString()) {
            return time;
        }
        if (dayDifference >= 1 && dayDifference < 2) {
            return `Yesterday ${time}`;
        }
        const year = targetDate.getFullYear();
        const month = (targetDate.getMonth() + 1).toString().padStart(2, '0');
        const day = targetDate.getDate().toString().padStart(2, '0');

        return `${year}/${month}/${day} ${time}`;
    }

    const Logout = async () => {

        const IsConfirmed = confirm("mau logout?")
        if (!IsConfirmed) {
            return
        }

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
    const getThey = () => (new URLSearchParams(window.location.search)).get('with') || '';

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

        const protocol = location.protocol === "http" ? "ws" : "wss"
        const port = location.port
        const fullWbsocketLocation = `${'ws'}://${location.hostname}:${port}/`
        const socket = new WebSocket(fullWbsocketLocation);
        const token = getCookie('X-ChatAppAccessToken') || null;


        const bubbles = document.querySelectorAll('.bubble');
        if (bubbles) {

            function updateBubble(bubble) {
                const startX = Math.random() * 100;
                const startY = Math.random() * 100;
                const endX = Math.random() * 100;
                const endY = Math.random() * 100;
                const duration = 10;

                bubble.style.width = `${Math.random() * 250 + 100}px`;
                bubble.style.height = bubble.style.width;
                bubble.style.left = `${startX}%`;
                bubble.style.top = `${startY}%`;
                bubble.style.animation = `moveBubble ${duration}s ease-in-out infinite`;

                bubble.style.setProperty('--start-x', `calc(${startX}% - 50%)`);
                bubble.style.setProperty('--start-y', `calc(${startY}% - 50%)`);
                bubble.style.setProperty('--end-x', `calc(${endX}% - 50%)`);
                bubble.style.setProperty('--end-y', `calc(${endY}% - 50%)`);
                setTimeout(() => updateBubble(bubble), duration * 1000);

            }

            bubbles.forEach((bubble, index) => {
                const delay = index * 500;
                setTimeout(() => {
                    updateBubble(bubble);
                    setInterval(() => updateBubble(bubble), 10000);
                }, delay);
            });
        }



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
                    if (socket.readyState === WebSocket.OPEN) {
                        socket.send(JSON.stringify({
                            token: getCookie('X-ChatAppAccessToken'),
                            data: {
                                action: 'message',
                                to: getThey(),
                                message: message,
                            }
                        }));

                        document.getElementById('msg').value = '';

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
                const mynode = `user${myid}`;

                if (fromUserId !== mynode) {
                    // console.log("bukan dari saya");
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
                        // console.error("Elemen dengan id " + fromUserId + "' tidak ditemukan");

                        const parent = document.getElementById('userlist')
                        const parentOldhtml = parent.innerHTML;
                        const newnode = `<a  onclick="nav(/messages?with=${fromUserId.replace('user','')})" class="pb-3 sm:pb-4" id="${fromUserId}">
                        <div class="flex my-1 items-center space-x-4 p-2 px-4 hover:shadow hover:bg-slate-200/10 hover:rounded-md border-cyan-200/30 border-b-2" style="border-bottom-width: 1px;">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-200 truncate">user${fromUserId.replace('user','')}</p>
                                <p class="text-sm text-gray-500 truncate">#${fromUserId.replace('user','')}</p>
                            </div>
                        </div>
                    </a>`

                        parent.innerHTML = newnode + parentOldhtml
                        showAlert("Hei There a new Message!", false)

                    }
                } else {
                    const theirnode = document.getElementById(fromUserId);

                    if (!theirnode) {
                        const id = fromUserId.replace('user', '')
                        if (myid == parseInt(id)) {
                            console.log("itu dari saya sendiri")
                            return


                        }
                        const parent = document.getElementById('userlist')
                        const parentOldhtml = parent.innerHTML;
                        const newnode = `<a onclick="nav(/messages?with=${fromUserId.replace('user','')})" class="pb-3 sm:pb-4" id="${fromUserId}">
                        <div class="flex my-1 items-center space-x-4 p-2 px-4 hover:shadow hover:bg-slate-200/10 hover:rounded-md border-cyan-200/30 border-b-2" style="border-bottom-width: 1px;">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-200 truncate">user${fromUserId.replace('user','')}</p>
                                <p class="text-sm text-gray-500 truncate">#${fromUserId.replace('user','')}</p>
                            </div>
                        </div>
                    </a>`

                        parent.innerHTML = newnode + parentOldhtml

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






        async function GetToken() {
            if (!token) {
                token = await fetch('/api/auth/token').then(res => res.json());
            }
        }
    });
</script>