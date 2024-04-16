<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel=”stylesheet” href=”https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css”/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../components.css">
    <title>Document</title>
    <?php

    $blockStructure =
        [
            ['Type' => 'L',
                'ComponentId' => 1],
            ['Type' => 'L',
                'ComponentId' => 2]

        ];
    ?>
</head>

<?php
require_once "../Components/header.php"
?>
<body class="d-flex flex-column min-vh-100">

<div class="container-fluid d-flex  flex-grow-1">
    <div class="container-xxl p-5">
        <div class="row flex-nowrap">
            <div class="col-1" style="min-width: 50px;">
                <div id="nav-panel"  class="p-0 sticky-top mt-5 border" style="top: 4em; min-width: 50px; max-width: 100px">
                    <ul id="nav-items" class="nav p-0 flex-column align-items-center">

                        <?php
                        require_once "RenderService.php";
                        foreach ($blockStructure as $block) {
                            generateNavButton($block['Type'], $block['ComponentId']);
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class="text-wrap col-10" style="text-align: justify">
                <div id="container" class=" container-fluid">
                    <!--Block content will be loaded here -->
                </div>
            </div>
        </div>

    </div>

</div>

<?php
require_once "../Components/footer.php"
?>
</body>
<script src="../jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<script>
    // const blockStructure = [
    //     {
    //         Type: "L",
    //         ComponentId: 1
    //     },
    //     {
    //         Type: "T",
    //         ComponentId: 2
    //     }
    // ]
    $(document).ready(function () {
        // blockStructure.forEach(e => generateNavButton(e.Type, e.ComponentId))


        // Set up click event for navigation links
        $('#nav-panel .nav-link').click(function (e) {
            e.preventDefault();

            window.scrollTo({top: 0, behavior: "auto"});
            let componentId = $(this).data('component-id');
            loadComponent(componentId);



        });

        // Load initial block
        loadComponent($("#nav-panel .nav-link").data("component-id"));


        // function generateNavButton(componentButtonType, componentId) {
        //     const Svgs = ['<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-book" viewBox="0 0 16 16">' +
        //     '<path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>' +
        //     '</svg>', ' <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"> <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/> <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/> </svg>']
        //     let svgCode = componentButtonType === "L" ? Svgs[0] : Svgs[1] // replace with the desired SVG code
        //
        //     let listItem = $('<li class="nav-item"></li>');
        //     let link = $('<a class="nav-link active" href="#" data-component-id="' + componentId + '"></a>');
        //     link.append(svgCode);
        //     listItem.append(link);
        //     $('#nav-items').append(listItem);
        //
        // }


        function loadComponent(componentId) {
            $.ajax({
                url: 'RenderService.php',
                type: 'GET',
                data: {"componentId": componentId},
                success: function (html) {
                    if (html) {
                        $('#container').html(html);
                        // Add active class to selected navigation link
                        $('#nav-panel .nav-link').removeClass('active');
                        $('[data-component-id="' + componentId + '"]').addClass('active');
                    } else {
                        $('#container').html('<p>Block not found.</p>');
                    }
                },
                error: function () {

                    let error = $('<div>Error loading block.</div>').addClass("card").appendTo("#container")
                    $('#container').html(error);
                }
            });
        }
    });


</script>
</html>
