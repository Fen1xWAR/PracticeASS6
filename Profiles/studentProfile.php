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
<div class="container mt-4" id="result">

</div>
<script src="../script.js"></script>
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
                let response = JSON.parse(data)
                if(response["html"]){
                    const $container = $('#result')
                    $container.html(response['html'])
                }



            },
            error: ajaxErrorHandling
        });
    }


    $('select').on('change', function () {

        loadTestResults(this.value)
    });
</script>
<?php