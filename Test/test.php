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

                    <div class="card mb-3">
                        <div class="card card-header"></div>
                        <div class="card-body p-3"><p class="card-text"> Тестовый вопрос с одним вариантом ответов </p>
                            <div class="form-check mb-2"><input class="form-check-input" type="radio" name="question"
                                                                id="option1" value="option1"> <label
                                        class="form-check-label" for="option1"> Первый вариант </label></div>
                            <div class="form-check mb-2"><input class="form-check-input" type="radio" name="question"
                                                                id="option2" value="option2"> <label
                                        class="form-check-label" for="option2"> Второй вариант </label></div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="question"
                                                           id="option3" value="option3"> <label class="form-check-label"
                                                                                                for="option3"> Третий
                                    вариант </label></div>
                        </div>
                        <div class="card-footer p-2">
                            <button type="button" id="backButton" class="btn btn-primary" disabled="">Назад</button>
                            <button type="button" id="nextButton" class="btn btn-primary float-right">Далее</button>
                        </div>
                    </div>

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
    let questions = [];

    function loadComponent(componentId) {
        $.ajax({
            url: 'RenderService.php',
            type: 'GET',
            data: {"componentId": componentId},
            success: function (data) {
                // console.log(data)
                const componentData = JSON.parse(data)
                questions = componentData['questions'];
                if (componentData['html']) {
                    $('#container').html(componentData['html']);
                    // Add active class to selected navigation link
                    $('#nav-panel .nav-link').removeClass('active');
                    $('[data-component-id="' + componentId + '"]').addClass('active');
                } else {
                    $('#container').html('<p>Block not found.</p>');
                }
            },
            error: function (error) {
                console.error(error);
                let errorM = $('<div>Error loading block.</div>').addClass("card").appendTo("#container")
                $('#container').html(errorM);
            }
        });
    }

    let userAnswers = [];

    function sendAnswers() {

        userAnswers = userAnswers.map(item => {
            if (typeof item === 'string' && item.includes(',')) {
                return item.replace(',', '').split('').sort().join('');
            } else {
                return item.toString();
            }
        })
        console.log(userAnswers)
        $.ajax({
            type: "POST",
            url: 'renderService.php',
            data: {"userAnswers": userAnswers},
            success: function (data) {
                $("#container").html(data)
            },
        });
    }

    function displayQuestion(currentQuestionNumber) {
        const wrapper = $('<div class="card"></div>');
        const testHeader = $('<div class="card card-header"><?php  echo $_SESSION['currentComponentData']['Title'] ?? "" ?></div>');
        const question = questions[currentQuestionNumber];
        const questionText = question['QuestionText'];
        const questionCategory = question['QuestionCategory'];
        const answersVariants = question['AnswersVariants'];


        const questionElement = $('<div class="card-body p-3"></div>');
        const questionTextElement = $('<p class="card-text" </p>').text(questionText);
        questionElement.append(questionTextElement);

        switch (questionCategory) {

            //Радиобатоны
            case 0:
                const radioInputs = answersVariants.map((answer, index) => {
                    const radioInput = $('<input type="radio"  class="form-check-input" id="' + (index + 1) + '" name="question">');
                    const radioLabel = $('<label class="form-check-label" for="' + (index + 1) + '"></label>').text(answer);
                    const radioDiv = $('<div class="form-check mb-2"></div>');
                    radioDiv.append(radioInput);
                    radioDiv.append(radioLabel);
                    radioInput.click(() => {
                        userAnswers[currentQuestionNumber] = index + 1;
                    });
                    return radioDiv;

                });
                questionElement.append(radioInputs);
                break;

            //Чекбоксы

            case 1:
                const checkboxInputs = answersVariants.map((answer, index) => {
                    const checkboxInput = $('<input type="checkbox" class="form-check-input" id="' + (index + 1) + '" value="' + (index + 1) + '">');
                    const checkboxLabel = $('<label class="form-check-label" for="' + (index + 1) + '"></label>').text(answer);
                    const checkboxDiv = $('<div class="form-check mb-2"></div>');
                    checkboxDiv.append(checkboxInput);
                    checkboxDiv.append(checkboxLabel);

                    // Collect user's answer for this checkbox
                    checkboxInput.click(() => {
                        if (checkboxInput.is(':checked')) {
                            userAnswers[currentQuestionNumber] = userAnswers[currentQuestionNumber] || [];
                            userAnswers[currentQuestionNumber].push(checkboxInput.val());
                        } else {
                            userAnswers[currentQuestionNumber] = userAnswers[currentQuestionNumber].filter(value => value !== checkboxInput.val());
                        }
                    });

                    return checkboxDiv;
                });
                questionElement.append(checkboxInputs);
                break;
            //Текстовое поле
            case 2:
                const textInput = $('<input type="text" id="1" class="form-control">');
                const textLabel =$('<label class="form-label" for="1"></label>').text("Введите ответ:");
                const textDiv =$('<div class="mb-2"></div>');
                textDiv.append(textLabel);
                textDiv.append(textInput);

                textInput.change(() => {
                    userAnswers[currentQuestionNumber] = textInput.val();
                });
                questionElement.append(textDiv);
                break;

        }

        const buttonsElement = $('<div class="card-footer d-flex justify-content-around p-2"></div>');
        const backButton = $('<button type="button" id="backButton"  class="btn btn-primary">Назад</button>');
        backButton.click(() => {
            console.log(typeof (userAnswers[currentQuestionNumber]))
            Array.isArray(userAnswers[currentQuestionNumber]) ? userAnswers[currentQuestionNumber].sort().join('') : userAnswers[currentQuestionNumber]
            currentQuestionNumber--;
            userAnswers[currentQuestionNumber] = []
            displayQuestion(currentQuestionNumber);
            console.log(currentQuestionNumber)
            if (currentQuestionNumber === 0) {
                $("#backButton").prop('disabled', true)
            }


            $("#nextButton").prop('disabled', false)

        });
        if (currentQuestionNumber === 0) {
            backButton.prop('disabled', true);
        }
        buttonsElement.append(backButton);

        const nextButton = $('<button type="button" id="nextButton" class="btn btn-primary ">Далее</button>');
        nextButton.click(() => {
            console.log(typeof (userAnswers[currentQuestionNumber]))

            currentQuestionNumber++;
            if (questionCategory === 0) {
                userAnswers[currentQuestionNumber - 1] = userAnswers[currentQuestionNumber - 1] || '';
            } else if (questionCategory === 1) {
                userAnswers[currentQuestionNumber - 1] = userAnswers[currentQuestionNumber - 1] ? userAnswers[currentQuestionNumber - 1].join(',') : '';
            }
            displayQuestion(currentQuestionNumber);
            $("#backButton").prop("disabled", false)

            if (currentQuestionNumber === questions.length - 1) {
                let nextButton = $("#nextButton")
                nextButton.text('Завершить');
                nextButton.removeClass('btn-primary').addClass('btn-success');
                nextButton.off('click').click(() => {
                    sendAnswers();
                    console.log('Finish button clicked');

                })
            }
            userAnswers[currentQuestionNumber] = []


        });


        buttonsElement.append(nextButton);

        questionElement.append(buttonsElement);
        wrapper.append(testHeader)
        wrapper.append(questionElement)

        $('#container').html(wrapper);
    }


    $(document).ready(function () {
        $('#container').empty();
        $('#nav-panel .nav-link').click(function (e) {
            e.preventDefault();

            window.scrollTo({top: 0, behavior: "auto"});
            let componentId = $(this).data('component-id');
            loadComponent(componentId);


        });

        // Load initial block
        loadComponent(  $("#nav-panel .nav-link").data("component-id"));

    });




</script>
</html>
