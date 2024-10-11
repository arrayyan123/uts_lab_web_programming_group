<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto flex justify-center py-10">
        <div class="w-full md:w-2/3">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold flex items-center"><i class="fa fa-tasks mr-2"></i> Task Lists</h2>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <ul class="divide-y">
                        <li class="px-6 py-4 flex items-center">
                            <input type="checkbox" class="mr-2">
                            <div class="flex-grow">
                                <div class="font-semibold">Rejected title<span class="ml-2 text-red-600">Rejected</span></div>
                                <div class="text-sm text-gray-500"><i>By Arrayan</i></div>
                            </div>
                            <div class="flex space-x-2">
                                <button class="text-green-500"><i class="fa fa-check"></i></button>
                                <button class="text-red-500"><i class="fa fa-trash"></i></button>
                            </div>
                        </li>
                        <li class="px-6 py-4 flex items-center">
                            <input type="checkbox" class="mr-2">
                            <div class="flex-grow">
                                <div class="font-semibold">New title</div>
                                <div class="text-sm text-gray-500">By Savero <span class="ml-2 text-blue-500">NEW</span></div>
                            </div>
                            <div class="flex space-x-2">
                                <button class="text-green-500"><i class="fa fa-check"></i></button>
                                <button class="text-red-500"><i class="fa fa-trash"></i></button>
                            </div>
                        </li>
                        <li class="px-6 py-4 flex items-center">
                            <input type="checkbox" class="mr-2">
                            <div class="flex-grow">
                                <div class="font-semibold">Casual title</div>
                                <div class="text-sm text-gray-500">By Kaname Madoka</div>
                            </div>
                            <div class="flex space-x-2">
                                <button class="text-green-500"><i class="fa fa-check"></i></button>
                                <button class="text-red-500"><i class="fa fa-trash"></i></button>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="px-6 py-4 text-right">
                    <button class="mr-2 text-gray-500">Cancel</button>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded">Add Task</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>