<div id="select-container" class="container-xxl d-flex flex-column align-items-center   ">


<select id="group-select" class="form-select w-50" aria-label="Выбор группы">


    <?php


    global $dbh;

    $query = $dbh->prepare("SELECT group_id, group_name FROM education_system.groups WHERE teacher_id = (SELECT id FROM teachers WHERE user_id = :userID)");
    $query->bindValue(":userID", $_SESSION['userID']);
    $query->execute();
    $result = $query->fetchAll();
    $selectedGroupId = $_SESSION['selectedGroupId'] ?? null;

    echo " <option value='' disabled " . ($selectedGroupId === null ? ' selected' : '') . ">Выберите группу:</option>";
    foreach ($result as $group) {
        $selected = $group['group_id'] == $selectedGroupId ? 'selected' : '';
        echo "<OPTION VALUE='" . $group['group_id'] . "' " . $selected . ">" . $group['group_name'] . "</OPTION>";
    }
    ?>

</select>
<select class="form-select mt-3 d-none w-50" id="block-select" aria-label="Выбор раздела">
<?php
$query = $dbh->prepare("SELECT block_id, block_name FROM blocks");
$query->execute();
$result = $query->fetchAll();

echo " <option value='' selected disabled >Выберите раздел:</option>";
foreach ($result as $block) {
    echo "<OPTION VALUE='" . $block['block_id']. "'>" . $block['block_name'] . "</OPTION>";
}
?>

</select>
</div>
<div id="tableContainer" class="d-flex overflow-auto mt-4 flex-column">

</div>
<script src="../script.js"></script>
<script src="../jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<script>
    const $blockSelect = $('#block-select')
    const $groupSelect = $('#group-select')
    let groupId;
    function loadGroupData(groupId,blockId) {
        $.ajax({
            type: "GET",
            url: "Services/renderService.php",
            data: {"groupId": {
                        "groupId" : groupId,
                        "blockId" : blockId
                }},
            success: function (data) {
                const response = JSON.parse(data)
                if (response['html']) {

                    const $container = $('#tableContainer')

                    $container.html(response['html'])
                }


            },
            error: ajaxErrorHandling
        });
    }

    $(document).ready(function () {
        let value = $groupSelect.val()
        if (value){
            groupId = value;
            $blockSelect.addClass('d-block').removeClass('d-none')
        }

    })
    $groupSelect.on('change', function () {
        groupId = this.value
        $blockSelect.addClass('d-block').removeClass('d-none')
    });
    $blockSelect.on('change',function (){
        loadGroupData(groupId,this.value)
    })
</script>