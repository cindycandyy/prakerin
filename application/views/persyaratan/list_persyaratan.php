<?php
foreach ($data as $key => $rowdata) {
?>

    <ul>
        <li><?php echo $this->model_depag->getFieldDepag($rowdata->id_depag);echo "<br>";echo $this->model_depag->getFieldDepag2($rowdata->id_depag);?>
            <ul><?php
            for($a=0;$a<=substr_count($rowdata->syarat,"|");$a++){
                $kata = explode("|", $rowdata->syarat);
                ?>
                <li><?php echo $kata[$a]?></li>
                <?php
            }
                ?>
            </ul>
        </li>
    </ul>
<?php
}
?>