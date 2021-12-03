<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
$this->addExternalCss('/local/components/hpl.signature/themes/css/bootstrap.min.css');
$this->addExternalCss('/local/components/hpl.signature/themes/css/style.css');
$this->addExternalJs('/local/components/hpl.signature/themes/js/jquery-3.1.1.min.js');
$this->addExternalJs('/local/components/hpl.signature/themes/js/bootstrap.min.js');
$this->addExternalJs('/local/components/hpl.signature/themes/js/script.js');

CModule::includeModule('crm');
$store = $arResult['STORE'];
?>
<form action="<?= POST_FORM_ACTION_URI ?>" method="POST" enctype="multipart/form-data">
    <div id="wizard" role="application" class="wizard clearfix">
        <div class="content clearfix">
        <h6 id="wizard-h-0" tabindex="-1" class="title current">Thông tin</h6>
        <hr>
        <section id="wizard-p-0" role="tabpanel" aria-labelledby="wizard-h-0" class="body current" aria-hidden="false">
            <div class="row">
                <div class="col-sm-3">
                    <div class="text-center">
                        <a href="#">
                            <img class="avatar-user" src="https://haiphatland-bitrix24.s3.ap-southeast-1.amazonaws.com/resize_cache/18151/23365dd92c1f65a6eb81283cfddb6812/main/2e6/2e61b1b6c3b002ee6fdf63c55b7b13b0/76371f579e7756290f66.png" alt="avatar-user">
                        </a>
                        <div class="avartar-picker pt-2">
                            <h5><b><?=$store['LAST_NAME'].' '.$store['NAME']?></h5></b>
                            <p><?=$store['WORK_PHONE']?></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="" class="control-label">Nhà cung cấp</label>
                                <input type="text" name="supplier" placeholder="" class="form-control" value="<?=$store['supplier']?>">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="" class="control-label">Loại chữ ký</label>
                                <input type="text" name="type" placeholder="" class="form-control" value="<?=$store['type']?>">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="" class="control-label">Số CMND/CCCD</label>
                                <input type="text" name="id_card" placeholder="" class="form-control" value="<?=$store['id_card']?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="" class="control-label">Năm sử dụng</label>
                                <input type="text" name="use_time" placeholder="" class="form-control" value="<?=$store['use_time']?>">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="" class="control-label">Thời gian hết hạn</label>
                                <?

                                $APPLICATION->IncludeComponent(
                                    "bitrix:main.calendar",
                                    "",
                                    array(
                                        "SHOW_INPUT" => "Y",
                                        "HIDE_TIMEBAR" => "true",
                                        "INPUT_NAME" => 'date_end',
                                        'INPUT_ADDITIONAL_ATTR' => 'class="form-control" placeholder="Chọn thời gian" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
                                        'INPUT_VALUE' => $store['date_end'],
                                        "SHOW_TIME" => 'N'
                                    ),
                                    $component,
                                    array("HIDE_ICONS" => true)
                                );

                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="" class="control-label">Chữ ký</label>
                                <input type="hidden" name="img_signature" class="img_signature" value="<?=$store['img_signature']?>">
                                <div id="profile" class="">
                                    <div class="dashes">
                                        <div class="text-center" style="line-height: 14;"></div>
                                    </div>
                                    <img src="<?=$store['img_signature']?>" alt="" class="img-show" style="width: 100%">
                                </div>
                                <input type="file" id="mediaFile" name="file" accept="image/png"/>
                            </div>
                        </div>
                    </div>
                    <div data-bx-id="task-edit-footer" class="webform-buttons task-edit-footer-fixed pinable-block tasks-footer-buttons-container">
                        <div class="tasks-form-footer-container text-center">
                            <button name="apply" type="submit" value="save" data-bx-id="task-edit-submit" class="webform-small-button webform-small-button-accept">
                                <span class="webform-small-button-text">Cập nhập</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
</form>

