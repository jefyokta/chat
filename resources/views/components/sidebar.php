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