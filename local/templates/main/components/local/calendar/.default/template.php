 <div class="container">
     <?
        if($arResult['IS_AUTH'] == false)
        {
            ?><div>Вы не авторизованы что бы просматривать календарь</div><?
        }
        else {
            ?>
            <div id="app">
                <div class="filter">
                    <div class="select-item year">
                        <div class="year-select">
                            выбырите год:
                        </div>
                        <select id="year">
                        </select>
                    </div>
                    <div class="select-item month">
                        <div class="month-select">
                            выбырите год:
                        </div>
                        <select id="month">
                        </select>
                    </div>
                </div>
                <table class='table table-week-name'>
                    <tr>
                        <td class="cell cell_border cell_day">Понедельник</td>
                        <td class="cell cell_border cell_day">Вторник</td>
                        <td class="cell cell_border cell_day">Среда</td>
                        <td class="cell cell_border cell_day">Четверг</td>
                        <td class="cell cell_border cell_day">Пятница</td>
                        <td class="cell cell_border cell_day">Субота</td>
                        <td class="cell cell_border cell_day">Воскресенье</td>
                    </tr>
                </table>
                <table class='table table-cells'></table>
                <? include 'forms/save.form.php'; ?>
                <? include 'forms/update.form.php'; ?>
            </div>
            <?
        }
     ?>
 </div>