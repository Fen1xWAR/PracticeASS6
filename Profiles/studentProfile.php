<select class="form-select" data-live-search="true" aria-label="Default select example">
    <?php



    global $dbh;


    $result = $dbh->query("SELECT block_id, block_name FROM blocks");
    $blocks = $result->fetchAll(PDO::FETCH_ASSOC);

    echo " <option value='' selected disabled >Выберите раздел:</option>";
    foreach ($blocks as $block) {

        echo "<option value='" . $block['block_id'] . "'>" . $block['block_name'] . "</option>";
    }
    ?>
</select>
<?php
?>
<div class="container mt-4" >
    <div class="accordion p-4"  id="result">

    </div>
</div>

<script src="../script.js"></script>
<script src="../jquery-3.7.1.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<script>
    function loadTestResults(blockId) {
        $.ajax({
            type: "GET",
            url: "Services/renderService.php",
            data: {"sectionId": blockId},
            success: function (data) {

                let response = $.parseJSON(data);
                if (response["html"]) {
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