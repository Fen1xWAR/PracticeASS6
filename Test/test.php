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
</head>

<?php
require_once "../Components/header.php"
?>
<body class="d-flex flex-column min-vh-100">

<div class="container-fluid d-flex  flex-grow-1">
    <div class="container-xxl pt-1 p-0 p-sm-5">
        <div class="row flex-nowrap">
            <div class="col-1" style="min-width: 50px;">
                <div id="nav-panel" class="p-0 sticky-top mt-5 border"
                     style="top: 4em; min-width: 50px; max-width: 100px">
                    <ul id="nav-items" class="nav p-0 flex-column align-items-center">

                        <?php
                        require_once "RenderService.php";
                        if (isset($_GET['blockId'])) {

                            $blockStructure = getBLockStructure($_GET['blockId']);
                            foreach ($blockStructure as $block) {
                                generateNavButton($block['type'], $block['component_id']);
                            }
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
