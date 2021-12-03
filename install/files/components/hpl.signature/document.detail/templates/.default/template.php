<?php
defined('B_PROLOG_INCLUDED') || die;

use Hpl\Signature\Helper\Helper;

$this->addExternalCss('/local/components/hpl.signature/themes/css/font-awesome.min.css');
$this->addExternalCss('/local/components/hpl.signature/themes/css/bootstrap.min.css');
$this->addExternalCss('/local/components/hpl.signature/themes/css/style.css');
$this->addExternalJs('/local/components/hpl.signature/themes/js/jquery-3.1.1.min.js');
$this->addExternalJs('/local/components/hpl.signature/themes/js/bootstrap.min.js');
$this->addExternalJs('/local/components/hpl.signature/themes/js/bootstrap.bundle.min.js');
$this->addExternalJs('/local/components/hpl.signature/themes/js/script.js');
/** @var CBitrixComponentTemplate $this */
$store = $arResult['STORE'];
$userUpload = Helper::getMember($store['created_by']);
if ($store['extend'] === 'pdf') {
    $icon_file = '<img width="25" src="/local/components/hpl.signature/themes/images/pdf.png">';
} elseif ($store['extend'] === 'docx') {
    $icon_file = '<img width="25" src="/local/components/hpl.signature/themes/images/word.png">';
} else {
    $icon_file = '<img width="25" src="/local/components/hpl.signature/themes/images/xlsx.png">';
}
$signers = $arResult['SIGNERS'];
?>
<div class="document-detail">
    <div class="row">
        <div class="col-sm-8 pr-0">
            <div class="wizard clearfix box-left">
                <div class="header d-flex">
                    <h6 class="title current mr-auto">
                        Thông tin đề xuất
                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                    </h6>
                    <span class="txt-code">Mã đề xuất : <span class="number"><?= $store['id'] ?></span></span>
                </div>
                <div class="content">
                    <div class="row info-propose">
                        <div class="col-sm-4 left">
                            <div class="txt">
                                <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                                Người tạo :
                            </div>
                            <div class="txt">
                                <i class="fa fa-cube" aria-hidden="true"></i>
                                Nhóm đề xuất :
                            </div>
                            <div class="txt">
                                <i class="fa fa-rss" aria-hidden="true"></i>
                                Phòng ban :
                            </div>
                            <div class="txt">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                Tải lên lúc :
                            </div>
                            <div class="txt">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                Cập nhập mới nhất :
                            </div>
                            <div class="txt">
                                <i class="fa fa-file-text" aria-hidden="true"></i>
                                Loại file :
                            </div>
                        </div>
                        <div class="col-sm-6 right">
                            <div class="txt font-weight-bold">
                                <?= $userUpload['NAME'] ?> (<?= $userUpload['LOGIN'] ?>)
                            </div>
                            <div class="txt">
                                <?= $store['group_name'] ?>
                            </div>
                            <div class="txt">
                                <?= $store['department_name'] ?>
                            </div>
                            <div class="txt">
                                <?= $store['created_at'] = date('H:i d/m/Y', strtotime($store['created_at'])) ?>
                            </div>
                            <div class="txt">
                                <?= $store['updated_at'] = date('H:i d/m/Y', strtotime($store['updated_at'])) ?>
                            </div>
                            <div class="txt">
                                <?= $icon_file ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wizard clearfix">
                <div class="header d-flex">
                    <h6 class="title current mr-auto">
                        Thông tin đề xuất
                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                    </h6>
                    <span class="txt-code">Mã đề xuất : <span class="number"><?= $store['id'] ?></span></span>
                </div>
                <div class="content">
                    <iframe src="http://docs.google.com/gview?url=https://v236.x8top.net/tmp082020/cf/tailieu/2019/2/pdf/mau-don-xin-xac-nhan-dan-su-33292.pdf&embedded=true" width="100%" height="1000"></iframe>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="wizard clearfix box-right signer">
                <div class="header d-flex">
                    <h6 class="title current mr-auto">
                        Người ký
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                    </h6>

                </div>
                <div class="content">
                    <? foreach ($signers as $signer): ?>
                        <?
                        $user_signer = Helper::getMember($signer['user_id']);
                        $user_avatar = $user_signer['IMAGE'] ? $user_signer['IMAGE'] : 'data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2247.188%22%20height%3D%2254.063%22%20viewBox%3D%220%200%2047.188%2054.063%22%3E%3Cdefs%3E%3Cstyle%3E.cls-1%20%7B%20fill%3A%20%23fff%3B%20fill-rule%3A%20evenodd%3B%20%7D%3C/style%3E%3C/defs%3E%3Cpath%20id%3D%22Shape_2_copy_4%22%20data-name%3D%22Shape%202%20copy%204%22%20class%3D%22cls-1%22%20d%3D%22M47.18%2054.062c0-3.217-3.61-16.826-3.61-16.826%200-1.99-2.6-4.26-7.72-5.585a17.394%2017.394%200%200%201-4.887-2.223c-.33-.188-.28-1.925-.28-1.925l-1.648-.25c0-.142-.14-2.225-.14-2.225%201.972-.663%201.77-4.574%201.77-4.574%201.252.695%202.068-2.4%202.068-2.4%201.482-4.3-.738-4.04-.738-4.04a27.05%2027.05%200%200%200%200-7.918c-.987-8.708-15.847-6.344-14.085-3.5-4.343-.8-3.352%209.082-3.352%209.082l.942%202.56c-1.85%201.2-.564%202.65-.5%204.32.09%202.466%201.6%201.955%201.6%201.955.093%204.07%202.1%204.6%202.1%204.6.377%202.556.142%202.12.142%202.12l-1.786.217a7.1%207.1%200%200%201-.14%201.732c-2.1.936-2.553%201.485-4.64%202.4-4.032%201.767-8.414%204.065-9.193%207.16S-.012%2054.06-.012%2054.06h47.19z%22/%3E%3C/svg%3E';
                        ?>
                        <div class="intranet-user-profile-grid">
                            <a class="intranet-user-profile-grid-item"
                               href="/company/personal/user/<?= $signer['user_id'] ?>/">
                                <div href="/company/personal/user/<?= $signer['user_id'] ?>/"
                                     class="ui-icon ui-icon-common-user intranet-user-profile-user-avatar">
                                    <i style="background-image: url('<?= $user_avatar ?>');"></i>
                                </div>
                                <div class="intranet-user-profile-user-container">
                                    <div class="intranet-user-profile-user-name"><?= $user_signer['NAME'] ?></div>
                                    <div class="intranet-user-profile-user-position">
                                        <?= $user_signer['LOGIN'] ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <? endforeach; ?>
                </div>
            </div>
            <div class="wizard clearfix box-right">
                <div class="header d-flex">
                    <h6 class="title current mr-auto">
                        Lịch sử
                        <i class="fa fa-history" aria-hidden="true"></i>
                    </h6>

                </div>
                <div class="content">
                    <div class="row">
                        <div class="col-12">
                            <ul class="timeline timeline-left">
                                        <li class="timeline-inverted timeline-item">
                                            <div class="timeline-badge success"><img
                                                        src="https://bootdey.com/img/Content/avatar/avatar2.png"
                                                        alt="img" class="img-fluid"></div>
                                            <div class="timeline-panel">
                                                <div class="timeline-heading"><h4 class="timeline-title">Genelia</h4>
                                                    <p><small class="text-muted"><i class="fa fa-clock-o"></i> 11 hours
                                                            ago via Twitter</small></p></div>
                                                <div class="timeline-body"><p> Lorem ipsum dolor sit amet, consectetur
                                                        adipisicing elit. Libero laboriosam dolor perspiciatis omnis
                                                        exercitationem. Beatae, officia pariatur? Est cum veniam
                                                        excepturi. Maiores praesentium, porro voluptas
                                                        suscipit facere rem dicta, debitis.</p></div>
                                            </div>
                                        </li>
                                        <li class="timeline-inverted timeline-item">
                                            <div class="timeline-badge warning"><img
                                                        src="https://bootdey.com/img/Content/avatar/avatar2.png"
                                                        alt="img" class="img-fluid"></div>
                                            <div class="timeline-panel">
                                                <div class="timeline-heading"><h4 class="timeline-title">Ritesh
                                                        Deshmukh</h4></div>
                                                <div class="timeline-body"><p><img
                                                                src="https://via.placeholder.com/600x300/FFB6C1/000000"
                                                                alt="img" class="img-fluid"></p>
                                                    <p> Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                                                        Laudantium maiores odit qui est tempora eos, nostrum provident
                                                        explicabo dignissimos debitis vel! Adipisci eius voluptates, ad
                                                        aut recusandae
                                                        minus eaque facere.</p></div>
                                            </div>
                                        </li>
                                        <li class="timeline-inverted timeline-item">
                                            <div class="timeline-badge danger"><span class="font-12">2018</span></div>
                                            <div class="timeline-panel">
                                                <div class="timeline-heading"><h4 class="timeline-title">Lorem ipsum
                                                        dolor</h4></div>
                                                <div class="timeline-body"><p> Lorem ipsum dolor sit amet, consectetur
                                                        adipisicing elit. Repellendus numquam facilis enim eaque,
                                                        tenetur nam id qui vel velit similique nihil iure molestias
                                                        aliquam, voluptatem totam quaerat, magni
                                                        commodi quisquam.</p></div>
                                            </div>
                                        </li>
                                        <li class="timeline-inverted timeline-item">
                                            <div class="timeline-panel">
                                                <div class="timeline-heading"><h4 class="timeline-title">Lorem ipsum
                                                        dolor</h4></div>
                                                <div class="timeline-body"><p> Lorem ipsum dolor sit amet, consectetur
                                                        adipisicing elit. Voluptates est quaerat asperiores sapiente,
                                                        eligendi, nihil. Itaque quos, alias sapiente rerum quas odit!
                                                        Aperiam officiis quidem delectus libero,
                                                        omnis ut debitis!</p></div>
                                            </div>
                                        </li>
                                        <li class="timeline-inverted timeline-item">
                                            <div class="timeline-badge info"><i class="fa fa-save"></i></div>
                                            <div class="timeline-panel">
                                                <div class="timeline-heading"><h4 class="timeline-title">Lorem ipsum
                                                        dolor</h4></div>
                                                <div class="timeline-body"><p> Lorem ipsum dolor sit amet, consectetur
                                                        adipisicing elit. Nobis minus modi quam ipsum alias at est
                                                        molestiae excepturi delectus nesciunt, quibusdam debitis amet,
                                                        beatae consequuntur impedit nulla qui!
                                                        Laborum, atque.</p>
                                                    <hr>
                                                    <div class="btn-group">
                                                        <button type="button" data-toggle="dropdown"
                                                                class="btn btn-primary btn-sm dropdown-toggle"><i
                                                                    class="fa fa-cog"></i><span class="caret"></span>
                                                        </button>
                                                        <div class="dropdown-menu"><a href="javascript:void(0)"
                                                                                      class="dropdown-item">Action</a><a
                                                                    href="javascript:void(0)" class="dropdown-item">Another
                                                                action</a> <a href="javascript:void(0)"
                                                                              class="dropdown-item">Something else
                                                                here</a>
                                                            <div class="dropdown-divider"></div>
                                                            <a href="javascript:void(0)" class="dropdown-item">Separated
                                                                link</a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="timeline-inverted timeline-item">
                                            <div class="timeline-badge success"><i class="fa fa-graduation-cap"></i>
                                            </div>
                                            <div class="timeline-panel">
                                                <div class="timeline-heading"><h4 class="timeline-title">Lorem ipsum
                                                        dolor</h4></div>
                                                <div class="timeline-body"><p> Lorem ipsum dolor sit amet, consectetur
                                                        adipisicing elit. Deserunt obcaecati, quaerat tempore officia
                                                        voluptas debitis consectetur culpa amet, accusamus dolorum
                                                        fugiat, animi dicta aperiam, enim incidunt
                                                        quisquam maxime neque eaque.</p></div>
                                            </div>
                                        </li>
                                    </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
<style>
    #workarea-content {
        background-color: #eef2f4 !important;
    }
</style>