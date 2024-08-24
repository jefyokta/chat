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

<script>
    const userId = 2;
    const socket = new WebSocket('ws://localhost:9502');

    socket.onopen = function() {
        const userId = '12345';
        socket.send(JSON.stringify({
            type: 'auth',
            
        }));
    };

    socket.onmessage = function(event) {
        const messageData = JSON.parse(event.data);
        const messages = document.getElementById('messages');
        const messageElement = document.createElement('div');

        if (messageData.user_id === userId) {
            messageData.isSelf = true;
        }

        if (messageData.isSelf) {
            messageElement.className = "text-white bg-blue-500 rounded-lg p-2 mb-2 max-w-max ml-auto text-right";
        } else {
            messageElement.className = "text-gray-800 bg-gray-200 rounded-lg p-2 mb-2 max-w-max mr-auto text-left";
        }

        messageElement.innerHTML = `<strong>${messageData.user_id}:</strong> ${messageData.text}`;
        messages.appendChild(messageElement);
        messages.scrollTop = messages.scrollHeight;
    };

    socket.onclose = function() {
        console.log("Connection closed");
    };

    socket.onerror = function(error) {
        console.log("WebSocket error: " + error.message);
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
                from: `1`,
                to: 12,
                message: message
            }));
            document.getElementById('msg').value = '';
        }
    }
</script>