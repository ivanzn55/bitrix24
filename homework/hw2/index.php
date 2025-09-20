<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("ДЗ1: Отладка и логирование");
?>

<div class="list-howework">

    <div class="list-homework__part">

        <div class="list-homework__part-title">
            Часть 1
        </div>
        <ul>
            <li><a target="_blank" href="/homework/hw2/debug.log">Файл лога из п1 ДЗ</a></li>
            <li><a target="_blank" href="/homework/hw2/debug.php">Файл генерирущий файл </a></li>
            <li>
                <a target="_blank" href="<?=Otus\Helper::getPathToClassInAdmin(Otus\Debug\HistoryPage::class);?>">
                    Файл с классом кастомного логгера
                </a>
            </li>
            <li>
                <a target="_blank" href="<?=Otus\Helper::getPathToClassInAdmin(Otus\Event\Main::class);?>">
                    Файл вариант с использованием событий
                </a>
            </li>
        </ul>

    </div>

    <div class="list-homework__part">

        <div class="list-homework__part-title">
            Часть 2
        </div>
        <ul>
            <li><a target="_blank" href="/homework/hw2/error.log">Файл лога из п2 ДЗ</a></li>
            <li><a target="_blank"
                   href="/homework/hw2/error.php">
                    Файл генерирущий файл
                </a>
            </li>
            <li><a target="_blank"
                   href="<?=Otus\Helper::getPathToClassInAdmin(Otus\Debug\Log::class);?>">
                    Файл с классом кастомного логгера</a>
            </li>
        </ul>

    </div>

</div>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>