<select class="form-select" aria-label="Default select example">
    <?php
    require_once "pdoConnection.php";


    global $dbh;

    $query = $dbh->prepare("SELECT component_id, data FROM components WHERE type ='Test' ");
    $query->execute();
    $result = $query->fetchAll();


    echo " <option value='' selected disabled >Выберите тест:</option>";
    foreach ($result as $test) {
        $testTitle = json_decode($test['data'], true)['Title'];

        echo "<OPTION VALUE='" . $test['component_id'] . "' >" . $testTitle . "</OPTION>";
    }
    ?>
</select>
<div class="accordion mt-4" id="resultAccordion">
<!--    <div class="accordion-item">-->
<!--        <h2 class="accordion-header">-->
<!--            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"-->
<!--                    aria-expanded="true" aria-controls="collapseOne">-->
<!--                Accordion Item #1-->
<!--            </button>-->
<!--        </h2>-->
<!--        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#resultAccordion">-->
<!--            <div class="accordion-body">-->
<!--                <table class="table">-->
<!--                    <thead>-->
<!--                    <tr>-->
<!--                        <th scope="col">#</th>-->
<!--                        <th scope="col">First</th>-->
<!--                        <th scope="col">Last</th>-->
<!--                        <th scope="col">Handle</th>-->
<!--                    </tr>-->
<!--                    </thead>-->
<!--                    <tbody>-->
<!--                    <tr>-->
<!--                        <th scope="row">1</th>-->
<!--                        <td>Mark</td>-->
<!--                        <td>Otto</td>-->
<!--                        <td>@mdo</td>-->
<!--                    </tr>-->
<!--                    <tr>-->
<!--                        <th scope="row">2</th>-->
<!--                        <td>Jacob</td>-->
<!--                        <td>Thornton</td>-->
<!--                        <td>@fat</td>-->
<!--                    </tr>-->
<!--                    <tr>-->
<!--                        <th scope="row">3</th>-->
<!--                        <td colspan="2">Larry the Bird</td>-->
<!--                        <td>@twitter</td>-->
<!--                    </tr>-->
<!--                    </tbody>-->
<!--                </table>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div class="accordion-item">-->
<!--        <h2 class="accordion-header">-->
<!--            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"-->
<!--                    data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">-->
<!--                Accordion Item #2-->
<!--            </button>-->
<!--        </h2>-->
<!--        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#resultAccordion">-->
<!--            <div class="accordion-body">-->
<!--                <table class="table">-->
<!--                    <thead>-->
<!--                    <tr>-->
<!--                        <th scope="col">#</th>-->
<!--                        <th scope="col">First</th>-->
<!--                        <th scope="col">Last</th>-->
<!--                        <th scope="col">Handle</th>-->
<!--                    </tr>-->
<!--                    </thead>-->
<!--                    <tbody>-->
<!--                    <tr>-->
<!--                        <th scope="row">1</th>-->
<!--                        <td>Mark</td>-->
<!--                        <td>Otto</td>-->
<!--                        <td>@mdo</td>-->
<!--                    </tr>-->
<!--                    <tr>-->
<!--                        <th scope="row">2</th>-->
<!--                        <td>Jacob</td>-->
<!--                        <td>Thornton</td>-->
<!--                        <td>@fat</td>-->
<!--                    </tr>-->
<!--                    <tr>-->
<!--                        <th scope="row">3</th>-->
<!--                        <td colspan="2">Larry the Bird</td>-->
<!--                        <td>@twitter</td>-->
<!--                    </tr>-->
<!--                    </tbody>-->
<!--                </table>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
</div>
<script src="../jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<script>
    function loadTestResults(testId) {
        $.ajax({
            type: "GET",
            url: "../Test/renderService.php",
            data: {"testId": testId},
            success: function (data) {
                console.log(data)

                let container = $('#resultAccordion')
                container.empty()
                $(data).appendTo(container)

            },
            error: function (data) {
                console.log('An error occurred: ' + data);
                //Сделать всплывашку с ошибкой
            },
        });
    }


    $('select').on('change', function () {

        loadTestResults(this.value)
    });
</script>
<?php