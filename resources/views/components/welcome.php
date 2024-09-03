<div class="w-full md:w-3/4 md:right-0 lg:w-4/5 absolute h-full flex flex-col justify-center items-center backdrop-blur-lg rounded-md py-2">
    <div class="w-full overflow-hidden relative h-full bg-blue-950/50 backdrop-blur-lg rounded-md flex flex-col justify-center items-center ">

        <div class="bubble bg-indigo-400/80"></div>
        <div class="bubble bg-teal-300/80"></div>
        <div class="bubble bg-blue-300/80"></div>
        <div class="bubble bg-indigo-300/80"></div>
        <div class="bubble bg-purple-400/80"></div>
        <div class="bubble bg-pink-300/80"></div>
      
        <div class="px-5 py-10 bg-slate-950/10 rounded-md shadow-lg backdrop-blur ease-in-out flex flex-col" id="chatapp">
            <h1 class="text-4xl text-cyan-300 font-semibold text-center mb-5 ">

                ChatApp</h1>
            <button class="md:hidden text-cyan-100 mx-5 text-center w-max/6 p-2 bg-cyan-200/20 rounded-md mb-5" onclick="showSidebar()">Open Chat
            </button>
            <div class="px-3 py-5 bg-slate-500/10 shadow-lg rounded-md">
                <p class="text-center text-yellow-400">Search Users</p>
                <form action="/search" class="my-5">
                    <input type="text" class="bg-slate-300/10 w-full h-10 border-0 text-white rounded-md text-center" name="username" placeholder="Username......">
                </form>
            </div>
        </div>

        <?php if (isset($search)) : ?>
            <h1 class="my-2 text-slate-200 font-semibold">Result </h1>
            <div class="bg-cyan-200/10 rounded-md py-5 px-5 w-full md:w-9/12  max-h-60">
                <div class=" rounded-md py-5 px-5 w-full max-h-full overflow-x-scroll ">
                    <?php foreach ($search as $s): ?>
                        <?php if ($s['id'] !== $userid) { ?>
                            <div class="flex justify-between mb-1 bg-white/30 p-2 rounded-md">
                                <h1 class="text-slate-900">
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
            </div>
        <?php endif; ?>
    </div>
</div>
<script>

</script>