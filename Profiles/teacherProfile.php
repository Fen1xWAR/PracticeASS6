<select class="form-select" aria-label="Default select example">


    <?php
    require_once "pdoConnection.php";


    global $dbh;

    $query = $dbh->prepare("SELECT group_id, group_name FROM education_system.groups WHERE teacher_id = (SELECT id FROM teachers WHERE user_id = :userID)");
    $query->bindValue(":userID", $_SESSION['userID']);
    $query->execute();
    $result = $query->fetchAll();
    $selectedGroupId = $_SESSION['selectedGroupId'] ?? null;

    echo " <option value='' disabled" . ($selectedGroupId === null ? ' selected' : '') . ">Выберите группу:</option>";
    foreach ($result as $group) {
        $selected = $group['group_id'] == $selectedGroupId ? 'selected' : '';
        echo "<OPTION VALUE='" . $group['group_id'] . "' " . $selected . ">" . $group['group_name'] . "</OPTION>";
    }
    ?>

</select>
<div id="tableContainer" class="d-flex mt-4 flex-column">

</div>
<script src="../jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<script>
    function loadGroupData(groupId) {
        $.ajax({
            type: "GET",
            url: "../Test/renderService.php",
            data: {"groupId": groupId},
            success: function (data) {
                // console.log('Transmission')
                if (data['error']) {
                    this.error(data['error']);
                } else {
                    // console.log(data)
                    let container = $('#tableContainer')
                    container.empty()
                    $(data).appendTo(container)
                }

            },
            error: function (data) {
                // console.log(data)
                console.log('An error occurred: ' + data);
                //Сделать всплывашку с ошибкой
            },
        });
    }

    $(document).ready(function () {
        let value = $('select').val()
        console.log(value)
        if (value) {
            loadGroupData(value)
        }

    })
    $('select').on('change', function () {

        loadGroupData(this.value)
    });
</script>