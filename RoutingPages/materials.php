<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybB5IXNxFwWQfE7u8Lj+XJHAxKlXiG/8rsrtpb6PEdzD828Ii" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../block.css">
    <title>Методические материалы</title>
    <style>
        .accordion-button {
            transition: background-color 0.2s ease;
        }

        .accordion-button:focus {
            box-shadow: none;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-light">
<?php
require_once "Components/header.php";
?>
<div class="container-fluid d-flex flex-column flex-grow-1 p-0">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Методические материалы</h1>
        <div class="accordion" id="materialsAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        История развития вычислительной техники
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#materialsAccordion">
                    <div class="accordion-body">
                        <p>История развития вычислительной техники охватывает несколько ключевых этапов, начиная с первых механических устройств, таких как счётные палочки и механические калькуляторы, до современных суперкомпьютеров и квантовых вычислений. Важнейшими вехами являются изобретение первых электронных вычислительных машин в середине 20 века, таких как ENIAC, а также развитие персональных компьютеров в 1980-х годах.</p>
                        <small>Дата: 2023-10-01</small>
                        <p>Полный текст материала: В начале 1940-х годов были разработаны первые электронные компьютеры, которые использовали вакуумные лампы для обработки данных. С развитием транзисторов в 1950-х годах компьютеры стали меньше и более надежными. В 1970-х годах с появлением интегральных схем началась эра персональных компьютеров, что значительно упростило доступ к вычислительной технике для широкой аудитории. В последние десятилетия наблюдается стремительное развитие технологий, включая облачные вычисления, искусственный интеллект и квантовые вычисления, которые открывают новые горизонты для вычислительных возможностей.</p>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Основополагающие принципы устройства ЭВМ
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#materialsAccordion">
                    <div class="accordion-body">
                        <p>Основополагающие принципы устройства ЭВМ включают архитектуру компьютера, которая описывает его основные компоненты: процессор, память и устройства ввода-вывода. Процессор выполняет вычисления и управляет работой других компонентов, в то время как память хранит данные и инструкции, необходимые для выполнения программ.</p>
                        <small>Дата: 2023-10-02</small>
                        <p>Полный текст материала: Архитектура фон Неймана, предложенная в 1945 году, стала основой для большинства современных компьютеров. Она предполагает, что данные и программы хранятся в одной и той же памяти, что позволяет процессору извлекать инструкции и данные последовательно. Важными аспектами являются также принципы работы с памятью, включая кэширование и виртуальную память, которые позволяют эффективно управлять ресурсами и ускорять выполнение программ.</p>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Программное обеспечение компьютера
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#materialsAccordion">
                    <div class="accordion-body">
                        <p>Программное обеспечение компьютера делится на системное и прикладное. Системное программное обеспечение, такое как операционные системы, управляет аппаратными ресурсами и предоставляет платформу для выполнения прикладных программ. Прикладное программное обеспечение предназначено для выполнения конкретных задач, таких как обработка текстов, работа с таблицами или графикой.</p>
                        <small>Дата: 2023-10-03</small>
                        <p>Полный текст материала: Операционные системы, такие как Windows, macOS и Linux, обеспечивают интерфейс между пользователем и аппаратным обеспечением. Они управляют процессами, памятью и устройствами ввода-вывода. Прикладные программы, такие как Microsoft Office, Adobe Photoshop и специализированные приложения, разрабатываются для решения конкретных задач и могут использовать системные ресурсы для выполнения своих функций. В последние годы наблюдается рост популярности облачных приложений, которые позволяют пользователям получать доступ к программному обеспечению через интернет.</p>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        Файловая система компьютера
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#materialsAccordion">
                    <div class="accordion-body">
                        <p>Файловая система компьютера отвечает за организацию, хранение и управление данными на носителях информации. Она определяет, как данные записываются, читаются и организуются в виде файлов и папок.</p>
                        <small>Дата: 2023-10-04</small>
                        <p>Полный текст материала: Существуют различные типы файловых систем, такие как FAT32, NTFS и ext4, каждая из которых имеет свои особенности и преимущества. Файловая система обеспечивает доступ к данным, управление правами доступа и защиту информации. Важным аспектом является также резервное копирование данных и восстановление после сбоев, что позволяет предотвратить потерю информации в случае аппаратных или программных ошибок.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once "Components/footer.php";
?>
</body>
</html>