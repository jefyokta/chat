<div class="flex w-full h-full">
    <div id="people" class="w-1/4 rounded-md fixed h-screen p-2">
        <div class="bg-slate-800 overflow-y-scroll	  h-full rounded-md ">
            <div class="p-5">
                <h1 class="text-slate-100 text-3xl"><?= $data['username']; ?></h1>
            </div>

            <ul class="max-w-md divide-y p-5 divide-gray-200 dark:divide-gray-700 overflow-y-scroll">
                <?php for ($i = 0; $i < 20; $i++): ?>
                    <li class="pb-3 sm:pb-4">
                        <div class="flex items-center space-x-4 rtl:space-x-reverse">
                            <div class="flex-shrink-0">
                                <img class="w-8 h-8 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-1.jpg" alt="Neil image">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                    Neil Sims
                                </p>
                                <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                    email@flowbite.com
                                </p>
                            </div>

                        </div>
                    </li>
                <?php endfor; ?>

                <li class="py-3 sm:py-4">
                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                        <div class="flex-shrink-0">
                            <img class="w-8 h-8 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-3.jpg" alt="Neil image">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                Bonnie Green
                            </p>
                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                email@flowbite.com
                            </p>
                        </div>

                    </div>
                </li>
                <li class="py-3 sm:py-4">
                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                        <div class="flex-shrink-0">
                            <img class="w-8 h-8 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-2.jpg" alt="Neil image">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                Michael Gough
                            </p>
                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                email@flowbite.com
                            </p>
                        </div>

                    </div>
                </li>
                <li class="py-3 sm:py-4">
                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                        <div class="flex-shrink-0">
                            <img class="w-8 h-8 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="Neil image">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                Thomas Lean
                            </p>
                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                email@flowbite.com
                            </p>
                        </div>

                    </div>
                </li>
                <li class="pt-3 pb-0 sm:pt-4">
                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                        <div class="flex-shrink-0">
                            <img class="w-8 h-8 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-4.jpg" alt="Neil image">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                Lana Byrd
                            </p>
                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                email@flowbite.com
                            </p>
                        </div>

                    </div>
                </li>
            </ul>

        </div>
    </div>
    <div class="w-3/4 right-0 absolute h-screen me-1 overflow-scroll">
        <div class="bg-slate-900 rounded-md mt-1 sticky top-0 justify-between top-0 w-full right-0 flex items-center mb-2 p-4 me-1">
            <h1 class="text-slate-300">username</h1>
            <h1 class="text-slate-300">username</h1>
        </div>
        <div id="messages" class="bg-white border h-screen  rounded p-4  py-24 overflow-y-scroll overflow-x-auto">
        </div>

        <div class="fixed w-3/4 bottom-0  right-0 flex items-center mb-2 p-4">
            <input type="text" id="msg" placeholder="Type your message..." class="border basis-full rounded-md py-2 px-4 mr-2 flex-grow">
            <button id="sendMessageBtn" class="bg-green-500 hover:bg-green-700 py-2 px-2 rounded-full w-auto text-white font-bold">
                <svg class="w-6 h-6 rotate-90 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 18-7 3 7-18 7 18-7-3Zm0 0v-5" />
                </svg>


            </button>
        </div>
    </div>
</div>
<div id="alert">

</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const socket = new WebSocket('ws://127.0.0.1:9501');
        const userId = 2; // Ganti sesuai ID pengguna Anda

        const token = getCookie('X-ChatAppAccessToken') || null;

        socket.onopen = function() {
            if (token) {
                socket.send(JSON.stringify({
                    type: 'auth',
                    token: token
                }));
            }
        };

        socket.onmessage = function(event) {
            const messageData = JSON.parse(event.data);
            console.log(messageData);
            // const messages = document.getElementById('messages');
            // const messageElement = document.createElement('div');

            // if (messageData.user_id === userId) {
            //     messageData.isSelf = true;
            // }

            // if (messageData.isSelf) {
            //     messageElement.className = "text-white bg-blue-500 rounded-lg p-2 mb-2 max-w-max ml-auto text-right";
            // } else {
            //     messageElement.className = "text-gray-800 bg-gray-200 rounded-lg p-2 mb-2 max-w-max mr-auto text-left";
            // }

            // messageElement.innerHTML = `<strong>${messageData.user_id}:</strong> ${messageData.text}`;
            // messages.appendChild(messageElement);
            // messages.scrollTop = messages.scrollHeight;
        };

        socket.onclose = function() {
            const toastHtml = `
            <div id="toast-danger" class="flex z-50 items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 " role="alert">
                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg ">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
                    </svg>
                    <span class="sr-only">Error icon</span>
                </div>
                <div class="ms-3 text-sm font-normal">WebSocket Closed</div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 " data-dismiss-target="#toast-danger" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
            `;

            const toastContainer = document.createElement('div');
            toastContainer.className = 'flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 rounded-lg fixed right-0 ms-5 z-50 bottom-0';
            toastContainer.innerHTML = toastHtml;

            document.body.appendChild(toastContainer);

            setTimeout(() => {
                toastContainer.remove();
            }, 5000);

            const closeButton = toastContainer.querySelector('[aria-label="Close"]');
            closeButton.addEventListener('click', () => {
                toastContainer.remove();
            });
        };

        socket.onerror = function(error) {
            console.error("WebSocket error: " + error.message);
            alert('WebSocket error');
        };

        document.getElementById('sendMessageBtn').addEventListener('click', sendMessage);
        document.getElementById('msg').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        function sendMessage() {
            const message = document.getElementById('msg').value.trim();
            if (message) {
                socket.send(JSON.stringify({
                    token: getCookie('X-ChatAppAccessToken'),
                    data: {
                        to: 2,
                        message: message,
                        action: 'message',

                    }
                }));
                document.getElementById('msg').value = '';
            }
        }

        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }
    });
</script>