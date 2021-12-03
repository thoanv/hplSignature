<?php
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Grid;
use Bitrix\Main\UI\Filter;
use Bitrix\Main\Web\Uri;
use Bitrix\Main\Web\Json;
use Hpl\Signature\Entity\HplSignatureDocumentsTable;


class DocumentListComponents extends CBitrixComponent
{
    const GRID_ID = 'DOCUMENT';
    const FORM_ID = 'DOCUMENT';
    const SORTABLE_FIELDS = array('name', 'status','created_at');
    const FILTERABLE_FIELDS = array('ID', 'NAME', 'ASSIGNED_BY_ID', 'ADDRESS');
    const SUPPORTED_ACTIONS = array('delete', 'copy');
    const SUPPORTED_SERVICE_ACTIONS = array('GET_ROW_COUNT');

    private $user;
    private static $headers;
    private static $filterFields;
    private static $filterPresets;

    public function __construct(CBitrixComponent $component = null)
    {
        global $USER;
        parent::__construct($component);
        self::$headers = array(
            array(
                'id' => 'extend',
                'name' => 'File',
                'default' => true,
            ),
            array(
                'id' => 'name',
                'name' => 'Tên đề xuất',
                'default' => true,
            ),
            array(
                'id' => 'created_by',
                'name' => 'Người gửi',
                'default' => true,
            ),
            array(
                'id' => 'signer',
                'name' => 'Trình ký đến',
                'default' => true,
            ),
            array(
                'id' => 'status',
                'name' => 'Trạng thái',
                'default' => true,
            ),

        );
        self::$filterFields = [
            array(
                'id' => 'name',
                'name' => 'Tiêu chí',
                'type' => 'text',
                'default' => true,

            ),
            array(
                'id' => 'status',
                'name' => 'Trạng thái',
                'type' => 'list',
                'default' => true,
                'items'=>[
                    'N'=>'Khoá',
                    'Y'=>'Mở khóa'
                ]

            ),

        ];
        self::$filterPresets = array(

        );
    }

    public function executeComponent()
    {
        if(!Loader::includeModule('hpl.signature')){
            ShowError('Not found module');
            return;
        }
        $context = Context::getCurrent();
        $request = $context->getRequest();

        $grid = new Grid\Options(self::GRID_ID);

        //region Sort
        $gridSort = $grid->getSorting();
        $sort = array_filter(
            $gridSort['sort'],
            function ($field) {
                return in_array($field, self::SORTABLE_FIELDS);
            },
            ARRAY_FILTER_USE_KEY
        );
        if (empty($sort)) {
            $sort = array('created_at' => 'DESC');
        }
        //endregion

        //region Filter
        $gridFilter = new Filter\Options(self::GRID_ID, self::$filterPresets);
        $gridFilterValues = $gridFilter->getFilter(self::$filterFields);
        $gridFilterValues = array_filter(
            $gridFilterValues,
            function ($fieldName) {
                return in_array($fieldName, self::FILTERABLE_FIELDS);
            },
            ARRAY_FILTER_USE_KEY
        );
        //endregion

        $this->processGridActions($gridFilterValues);
        $this->processServiceActions($gridFilterValues);

        //region Pagination
        $gridNav = $grid->GetNavParams();
        $pager = new PageNavigation('');
        $pager->setPageSize($gridNav['nPageSize']);
        $pager->setRecordCount(HplSignatureDocumentsTable::getCount($gridFilterValues));
        if ($request->offsetExists('page')) {
            $currentPage = $request->get('page');
            $pager->setCurrentPage($currentPage > 0 ? $currentPage : $pager->getPageCount());
        } else {
            $pager->setCurrentPage(1);
        }
        //endregion
        $stores = HplSignatureDocumentsTable::getList(
            array(
                'filter' => $gridFilterValues,
                'select' => [
                    'group_name' => 'GROUPS.name',
                    'cate_name'  => 'CATEGORY.name',
                    'department_name' => 'DEPARTMENT.NAME',
                    '*'
                ],
                'limit' => $pager->getLimit(),
                'offset' => $pager->getOffset(),
                'order' => $sort
        ));

        $requestUri = new Uri($request->getRequestedPage());
        $requestUri->addParams(array('sessid' => bitrix_sessid()));

        $this->arResult = array(
            'GRID_ID' => self::GRID_ID,
            'STORES' => $stores,
            'HEADERS' => self::$headers,
            'PAGINATION' => array(
                'PAGE_NUM' => $pager->getCurrentPage(),
                'ENABLE_NEXT_PAGE' => $pager->getCurrentPage() < $pager->getPageCount(),
                'URL' => $request->getRequestedPage(),
            ),
            'SORT' => $sort,
            'FILTER' => self::$filterFields,
            'FILTER_PRESETS' => self::$filterPresets,
            'ENABLE_LIVE_SEARCH' => false,
            'DISABLE_SEARCH' => true,
            'SERVICE_URL' => $requestUri->getUri(),
        );

        $this->includeComponentTemplate();
    }
    private function getStores($params = array())
    {
        $dbStores = HplSignatureDocumentsTable::getList($params);
        $stores = $dbStores->fetchAll();

        $userIds = array_column($stores, 'created_by');
        $userIds = array_unique($userIds);
        $userIds = array_filter(
            $userIds,
            function ($userId) {
                return intval($userId) > 0;
            }
        );

        $dbUsers = \Bitrix\Main\UserTable::getList(array(
            'filter' => array('=ID' => $userIds)
        ));
        $users = array();
        foreach ($dbUsers as $user) {
            $users[$user['ID']] = $user;
        }

        foreach ($stores as &$store) {
            if (intval($store['created_by']) > 0) {
                $store['created_by'] = $users[$store['created_by']];
            }
        }

        return $stores;
    }

    private function processGridActions($currentFilter)
    {
        if (!check_bitrix_sessid()) {
            return;
        }

        $context = Context::getCurrent();
        $request = $context->getRequest();

        $action = $request->get('action_button_' . self::GRID_ID);

        if (!in_array($action, self::SUPPORTED_ACTIONS)) {
            return;
        }

        $allRows = $request->get('action_all_rows_' . self::GRID_ID) == 'Y';
        if ($allRows) {
            $dbStores = HplSignatureDocumentsTable::getList(array(
                'filter' => $currentFilter,
                'select' => array('ID'),
            ));
            $storeIds = array();
            foreach ($dbStores as $store) {
                $storeIds[] = $store['ID'];
            }
        } else {
            $storeIds = $request->get('ID');
            if (!is_array($storeIds)) {
                $storeIds = array();
            }
        }

        if (empty($storeIds)) {
            return;
        }

        switch ($action) {
            case 'delete':
                foreach ($storeIds as $storeId) {
                    HplSignatureDocumentsTable::delete($storeId);
                }
                break;

            default:
                break;
        }
    }

    private function processServiceActions($currentFilter)
    {
        global $APPLICATION;

        if (!check_bitrix_sessid()) {
            return;
        }

        $context = Context::getCurrent();
        $request = $context->getRequest();

        $params = $request->get('PARAMS');

        if (empty($params['GRID_ID']) || $params['GRID_ID'] != self::GRID_ID) {
            return;
        }

        $action = $request->get('ACTION');

        if (!in_array($action, self::SUPPORTED_SERVICE_ACTIONS)) {
            return;
        }

        $APPLICATION->RestartBuffer();
        header('Content-Type: application/json');

        switch ($action) {
            case 'GET_ROW_COUNT':
                $count = HplSignatureDocumentsTable::getCount($currentFilter);
                echo Json::encode(array(
                    'DATA' => array(
                        'TEXT' => Loc::getMessage('CRMSTORES_GRID_ROW_COUNT', array('#COUNT#' => $count))
                    )
                ));
                break;

            default:
                break;
        }

        die;
    }
}

