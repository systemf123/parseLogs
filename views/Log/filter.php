<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
    <div class="site-index">
        <div class="jumbotron">
            <h1>Статистика запросов</h1>
        </div>
        <div class="filter">
            <div class="filter__caption" role="alert">
                <h2>Фильтр:</h2>
            </div>
            <?php
            $filterItem = [];
            foreach ($dataFilter['modelArchitecture'] as $modelArchitecture) {
                $filterItem[$modelArchitecture['architecture']] = $modelArchitecture['architecture'];
            }
            array_unshift ( $filterItem , '');
            ?>
            <?php $form = ActiveForm::begin(['id' => 'filter', 'action' => '?r=log/filter', 'options' => ['class' => 'filter-form']]); ?>
            <?= $form->field($formFilter, 'architecture')->dropDownList($filterItem);; ?>
            <?php
            $filterItem = [];
            foreach ($dataFilter['modelOS'] as $modelOS) {
                $filterItem[$modelOS['operation']] = $modelOS['operation'];
            }
            array_unshift ( $filterItem , '');
            ?>
            <?= $form->field($formFilter, 'operation')->dropDownList($filterItem);; ?>

            <?= $form->field($formFilter, 'timestampStart')->textInput(['rows' => 5, 'type' => 'datetime-local']); ?>
            <?= $form->field($formFilter, 'timestampEnd')->textInput(['rows' => 5, 'type' => 'datetime-local']); ?>
            <div class="form-group">
                <?= Html::submitButton('Отправить', ['class' => 'btn-sibmit btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="body-content">
            <?php if ($data) { ?>
                <div class="row">
                    <div class="col-lg-4">

                        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
                        <script type="text/javascript">

                          // Load the Visualization API library and the piechart library.
                          google.load('visualization', '1.0', {'packages':['corechart']});
                          google.setOnLoadCallback(drawChart);

                          function drawChart() {
                            var data = new google.visualization.DataTable();
                            data.addColumn('date', 'Дата');
                            data.addColumn('number', 'Запросов');
                            data.addRows([
                                <?
                                foreach($data as $log) {
                                    echo "[new Date(" . $log['Year'] . "," . $log['Month'] . "," . $log['Day'] . "," . $log['Hour'] . ")," . $log['CountRequest'] . "],\n";
                                }
                                ?>
                            ]);
                            var options = {
                              'title': 'Количество запросов в час:',
                              'width': 1200,
                              'height': 500,
                              'legend': {'position': 'none'},
                              'titleTextStyle': {'fontName': 'Georgia', 'fontSize': 20, 'bold': false},
                              chartArea: {width: '100%'},
                              vAxis: {textPosition: 'in', minValue: 0},
                            };

                            var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
                            chart.draw(data, options);
                          }
                        </script>

                        <div id="chart_div" style="width: 1200px;"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="filter__caption" role="alert">
                            <h2>Таблица запросов:</h2>
                        </div>

                        <table class="table table_sort">
                            <thead>
                            <tr>
                                <th>Дата и время</th>
                                <th>Число запросов</th>
                                <th>Самый популярный URL</th>
                                <th>Самый популярный браузер</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?
                            foreach($data as $val) { ?>
                                <tr>
                                    <td><?php echo date("Y-m-d H:i:s", mktime($val['Hour'], 0, 0, $val['Month'], $val['Day'], $val['Year'])); ?></td>
                                    <td><?php echo $val['CountRequest'] ?></td>
                                    <td><?php echo $val['urlLeader'] ?></td>
                                    <td><?php
                                        $browserName = '';
                                        $browserName = $val['Safari'] > $val['Chrome'] ? 'Safari' : 'Chrome';
                                        $browserName = $val[$browserName] > $val['Opera'] ? $browserName : 'Opera';
                                        $browserName = $val[$browserName] > $val['Firefox'] ? $browserName : 'Firefox';
                                        $browserName = $val[$browserName] > $val['IE11'] ? $browserName : 'IE11';
                                        echo $browserName;
                                        ?></td>
                                </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } else { ?>
                <div class="alert alert-no-data alert-warning" role="alert">Данных не найдено!</div>
            <?php } ?>
        </div>
    </div>
<?php
/*$js =
    <<<JS
$('body').on('beforeSubmit', '#filter', function() {
    var form = $(this);
    var data = form.serialize();
    var loader = $('.loader-wrap');
    
    loader.show()
    // отправляем данные на сервер
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: data
    })
    .done(function(data) {
        if (data.success) {
            $('#response').html(data.responseHTML);
            loader.hide()
        }
    })
    .fail(function () {
        loader.hide()
        alert('Произошла ошибка при отправке данных!');
    })
    return false; 
})
const getSort = ({ target }) => {
const order = (target.dataset.order = -(target.dataset.order || -1));
const index = [...target.parentNode.cells].indexOf(target);
const collator = new Intl.Collator(['en', 'ru'], { numeric: true });
const comparator = (index, order) => (a, b) => order * collator.compare(
  a.children[index].innerHTML,
  b.children[index].innerHTML
);

for(const tBody of target.closest('table').tBodies)
  tBody.append(...[...tBody.rows].sort(comparator(index, order)));

for(const cell of target.parentNode.cells)
  cell.classList.toggle('sorted', cell === target);
};

document.querySelectorAll('.table_sort thead').forEach(tableTH => tableTH.addEventListener('click', () => getSort(event)));
JS;

$this->registerJs($js, $this::POS_READY);
*/?>
