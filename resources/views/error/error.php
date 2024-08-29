<div class="p-10 bg-red-200 m-5 rounded-md">
    <p class="text-red-900  text-xl font-bold"><?= $message; ?></p>
    <div class="m-5">

        <span class="p-2 mt-5 bg-red-500 rounded-md">
            <span class="max-w-min ">On <b><?= $file; ?></b></span>
        </span>
        <p class="m-10"> On Line :<?= $line; ?>
            <span class="bg-slate-800 p-2 text-white rounded-sm"><?= $errorline; ?></span>
        </p>
    </div>
</div>