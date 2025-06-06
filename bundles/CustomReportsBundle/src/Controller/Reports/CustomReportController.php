<?php
declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\CustomReportsBundle\Controller\Reports;

use Exception;
use Pimcore\Bundle\CustomReportsBundle\Tool;
use Pimcore\Controller\Traits\JsonHelperTrait;
use Pimcore\Controller\UserAwareController;
use Pimcore\Extension\Bundle\Exception\AdminClassicBundleNotFoundException;
use Pimcore\Model\Element\Service;
use Pimcore\Model\Exception\ConfigWriteException;
use stdClass;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use function array_key_exists;
use function count;
use function is_array;
use function strlen;

/**
 * @internal
 */
#[Route('/custom-report')]
class CustomReportController extends UserAwareController
{
    use JsonHelperTrait;

    #[Route('/tree', name: 'pimcore_bundle_customreports_customreport_tree', methods: ['GET', 'POST'])]
    public function treeAction(): JsonResponse
    {
        $this->checkPermission('reports_config');
        $reports = Tool\Config::getReportsList();

        return $this->jsonResponse($reports);
    }

    #[Route(
        '/portlet-report-list',
        name: 'pimcore_bundle_customreports_customreport_portletreportlist',
        methods: ['GET', 'POST']
    )]
    public function portletReportListAction(): JsonResponse
    {
        $this->checkPermission('reports');
        $reports = Tool\Config::getReportsList($this->getPimcoreUser());

        return $this->jsonResponse(['data' => $reports]);
    }

    #[Route('/add', name: 'pimcore_bundle_customreports_customreport_add', methods: ['POST'])]
    public function addAction(Request $request): JsonResponse
    {
        $this->checkPermission('reports_config');

        $success = false;

        $reportName = $request->request->getString('name');
        $this->isValidConfigName($reportName);

        $report = Tool\Config::getByName($reportName);

        if (!$report) {
            $report = new Tool\Config();
            if (!$report->isWriteable()) {
                throw new ConfigWriteException();
            }

            $report->setName($reportName);
            $report->save();

            $success = true;
        }

        return $this->jsonResponse(['success' => $success, 'id' => $report->getName()]);
    }

    #[Route('/delete', name: 'pimcore_bundle_customreports_customreport_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request): JsonResponse
    {
        $this->checkPermission('reports_config');

        $report = Tool\Config::getByName($request->request->getString('name'));
        if (!$report) {
            throw $this->createNotFoundException();
        }
        if (!$report->isWriteable()) {
            throw new ConfigWriteException();
        }

        $report->delete();

        return $this->jsonResponse(['success' => true]);
    }

    #[Route('/clone', name: 'pimcore_bundle_customreports_customreport_clone', methods: ['POST'])]
    public function cloneAction(Request $request): JsonResponse
    {
        $this->checkPermission('reports_config');

        $newName = $request->request->getString('newName');
        $this->isValidConfigName($newName);
        $report = Tool\Config::getByName($newName);
        if ($report) {
            throw new Exception('report already exists');
        }

        $report = Tool\Config::getByName($request->request->getString('name'));
        if (!$report) {
            throw $this->createNotFoundException();
        }
        $reportData = $this->encodeJson($report);
        $reportData = $this->decodeJson($reportData);

        unset($reportData['name']);
        $reportData['name'] = $newName;

        foreach ($reportData as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($report, $setter)) {
                $report->$setter($value);
            }
        }

        $report->save();

        return $this->jsonResponse(['success' => true]);
    }

    #[Route('/get', name: 'pimcore_bundle_customreports_customreport_get', methods: ['GET'])]
    public function getAction(Request $request): JsonResponse
    {
        $this->checkPermissionsHasOneOf(['reports_config', 'reports']);

        $report = Tool\Config::getByName($request->query->getString('name'));
        if (!$report) {
            throw $this->createNotFoundException();
        }
        $data = $report->getObjectVars();
        $data['writeable'] = $report->isWriteable();

        return $this->jsonResponse($data);
    }

    #[Route('/update', name: 'pimcore_bundle_customreports_customreport_update', methods: ['PUT'])]
    public function updateAction(Request $request): JsonResponse
    {
        $this->checkPermission('reports_config');
        $reportName = $request->request->getString('name');
        $this->isValidConfigName($reportName);
        $report = Tool\Config::getByName($reportName);
        if (!$report) {
            throw $this->createNotFoundException();
        }
        if (!$report->isWriteable()) {
            throw new ConfigWriteException();
        }

        $data = $this->decodeJson($request->request->getString('configuration'));

        if (!is_array($data['yAxis'])) {
            $data['yAxis'] = strlen($data['yAxis'] ?? '') ? [$data['yAxis']] : [];
        }

        $adapter = Tool\Config::getAdapter($report->getDataSourceConfig());
        $pagination = $adapter->getPagination();
        $data['pagination'] = $pagination;

        foreach ($data as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($report, $setter)) {
                $report->$setter($value);
            }
        }

        $report->save();

        return $this->jsonResponse(['success' => true]);
    }

    #[Route('/column-config', name: 'pimcore_bundle_customreports_customreport_columnconfig', methods: ['POST'])]
    public function columnConfigAction(Request $request): JsonResponse
    {
        $this->checkPermission('reports_config');

        $report = Tool\Config::getByName($request->request->getString('name'));
        if (!$report) {
            throw $this->createNotFoundException();
        }
        $columnConfiguration = $report->getColumnConfiguration();

        $configuration = json_decode($request->request->getString('configuration'));
        $configuration = $configuration[0] ?? null;

        $success = false;
        $errorMessage = null;

        $result = [];

        try {
            $adapter = Tool\Config::getAdapter($configuration);
            $columns = $adapter->getColumnsWithMetadata($configuration);
            $columnNames = array_map(fn ($column) => $column->getName(), $columns);
            $columnMap = array_combine($columnNames, $columns);

            foreach ($columnConfiguration as $item) {
                $name = $item['name'];
                if (array_key_exists($name, $columnMap)) {
                    $result[] = $columnMap[$name];
                    unset($columnMap[$name]);
                }
            }
            foreach ($columnMap as $remainingColumn) {
                $result[] = $remainingColumn;
            }

            $success = true;
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }

        return $this->jsonResponse([
            'success' => $success,
            'columns' => $result,
            'errorMessage' => $errorMessage,
        ]);
    }

    #[Route('/get-report-config', name: 'pimcore_bundle_customreports_customreport_getreportconfig', methods: ['GET'])]
    public function getReportConfigAction(Request $request): JsonResponse
    {
        $this->checkPermission('reports');

        $reports = [];

        $list = new Tool\Config\Listing();
        $items = $list->getDao()->loadForGivenUser($this->getPimcoreUser());

        foreach ($items as $report) {
            if ($report->getDataSourceConfig() !== null) {
                $reports[] = [
                    'name' => htmlspecialchars($report->getName()),
                    'niceName' => htmlspecialchars($report->getNiceName()),
                    'iconClass' => htmlspecialchars($report->getIconClass()),
                    'group' => htmlspecialchars($report->getGroup()),
                    'groupIconClass' => htmlspecialchars($report->getGroupIconClass()),
                    'menuShortcut' => $report->getMenuShortcut(),
                    'reportClass' => htmlspecialchars($report->getReportClass()),
                ];
            }
        }

        return $this->jsonResponse([
            'success' => true,
            'reports' => $reports,
        ]);
    }

    #[Route('/data', name: 'pimcore_bundle_customreports_customreport_data', methods: ['POST'])]
    public function dataAction(Request $request): JsonResponse
    {
        $this->checkPermission('reports');
        if (!class_exists(\Pimcore\Bundle\AdminBundle\Helper\QueryParams::class)) {
            throw new AdminClassicBundleNotFoundException('This action requires package "pimcore/admin-ui-classic-bundle" to be installed.');
        }
        $offset = $request->request->getInt('start', 0);
        $limit = $request->request->getInt('limit', 40);
        $config = Tool\Config::getByName($request->request->getString('name'));
        if (!$config) {
            throw $this->createNotFoundException();
        }
        $configuration = $config->getDataSourceConfig();
        $adapter = Tool\Config::getAdapter($configuration, $config);
        $sortFilters = $this->getSortAndFilters($request, $configuration);
        $result = $adapter->getData($sortFilters['filters'], $sortFilters['sort'], $sortFilters['dir'], $offset, $limit, null, $sortFilters['drillDownFilters']);

        return $this->jsonResponse([
            'success' => true,
            'data' => $result['data'],
            'total' => $result['total'],
        ]);
    }

    #[Route(
        '/drill-down-options',
        name: 'pimcore_bundle_customreports_customreport_drilldownoptions',
        methods: ['POST']
    )]
    public function drillDownOptionsAction(Request $request): JsonResponse
    {
        $this->checkPermission('reports');

        $field = $request->request->getString('field');
        $filters = ($request->request->getString('filter') ? json_decode($request->request->getString('filter'), true) : null);
        $drillDownFilters = $request->request->all('drillDownFilters');

        $config = Tool\Config::getByName($request->request->getString('name'));
        if (!$config) {
            throw $this->createNotFoundException();
        }
        $configuration = $config->getDataSourceConfig();

        $adapter = Tool\Config::getAdapter($configuration, $config);
        $result = $adapter->getAvailableOptions($filters ?? [], $field, $drillDownFilters);

        return $this->jsonResponse([
            'success' => true,
            'data' => $result['data'],
        ]);
    }

    #[Route('/chart', name: 'pimcore_bundle_customreports_customreport_chart', methods: ['POST'])]
    public function chartAction(Request $request): JsonResponse
    {
        $this->checkPermission('reports');
        $config = Tool\Config::getByName($request->request->getString('name'));
        if (!$config) {
            throw $this->createNotFoundException();
        }
        $configuration = $config->getDataSourceConfig();
        $adapter = Tool\Config::getAdapter($configuration, $config);
        $sortFilters = $this->getSortAndFilters($request, $configuration);
        $result = $adapter->getData($sortFilters['filters'], $sortFilters['sort'], $sortFilters['dir'], null, null, null, $sortFilters['drillDownFilters']);

        return $this->jsonResponse([
            'success' => true,
            'data' => $result['data'],
            'total' => $result['total'],
        ]);
    }

    protected function getTemporaryFileFromFileName(string $exportFileName): string
    {
        $exportFileName = basename($exportFileName);
        if (!str_ends_with($exportFileName, '.csv')) {
            throw new InvalidArgumentException($exportFileName . ' is not a valid csv file.');
        }

        return PIMCORE_SYSTEM_TEMP_DIRECTORY . '/' . $exportFileName;
    }

    #[Route('/create-csv', name: 'pimcore_bundle_customreports_customreport_createcsv', methods: ['GET'])]
    public function createCsvAction(Request $request): JsonResponse
    {
        $this->checkPermission('reports');

        set_time_limit(300);

        $sort = $request->query->getString('sort');
        $dir = $request->query->getString('dir');
        $filters = $request->query->has('filter') ? json_decode(urldecode($request->query->getString('filter')), true) : null;
        $drillDownFilters = $request->query->getString('drillDownFilters');
        if ($drillDownFilters) {
            $drillDownFilters = json_decode($drillDownFilters, true);
        }
        $includeHeaders = $request->query->getBoolean('headers');

        $config = Tool\Config::getByName($request->query->getString('name'));
        if (!$config) {
            throw $this->createNotFoundException();
        }

        $columns = $config->getColumnConfiguration();
        $fields = [];
        foreach ($columns as $column) {
            if ($column['export']) {
                $fields[] = $column['name'];
            }
        }

        $configuration = $config->getDataSourceConfig();

        $adapter = Tool\Config::getAdapter($configuration, $config);

        $offset = $request->query->getInt('offset');
        $limit = 5000;
        $result = $adapter->getData($filters, $sort, $dir, $offset * $limit, $limit, $fields, $drillDownFilters);
        ++$offset;

        if (!($exportFile = $request->query->getString('exportFile'))) {
            $exportFile = PIMCORE_SYSTEM_TEMP_DIRECTORY . '/report-export-' . uniqid() . '.csv';
            @unlink($exportFile);
        } else {
            $exportFile = $this->getTemporaryFileFromFileName($exportFile);
        }

        $fp = fopen($exportFile, 'a');

        if ($includeHeaders) {
            fputcsv($fp, $fields, ';');
        }

        foreach ($result['data'] as $row) {
            $row = Service::escapeCsvRecord($row);
            fputcsv($fp, array_values($row), ';');
        }

        fclose($fp);

        $progress = $result['total'] ? ($offset * $limit) / $result['total'] : 1;
        $progress = $progress > 1 ? 1 : $progress;

        return new JsonResponse([
            'exportFile' => basename($exportFile),
            'offset' => $offset,
            'progress' => $progress,
            'finished' => empty($result['data']) || count($result['data']) < $limit,
        ]);
    }

    #[Route('/download-csv', name: 'pimcore_bundle_customreports_customreport_downloadcsv', methods: ['GET'])]
    public function downloadCsvAction(Request $request): BinaryFileResponse
    {
        $this->checkPermission('reports');
        if ($exportFile = $request->query->getString('exportFile')) {
            $exportFile = $this->getTemporaryFileFromFileName($exportFile);
            $response = new BinaryFileResponse($exportFile);
            $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'export.csv');
            $response->deleteFileAfterSend(true);

            return $response;
        }

        throw new FileNotFoundException("File \"$exportFile\" not found!");
    }

    /**
     * @throws Exception
     */
    public function isValidConfigName(string $configName): void
    {
        if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $configName)) {
            throw new Exception('The customer report name is invalid');
        }
    }

    // gets the sort, direction, filters, drilldownfilters from grid or initial config
    private function getSortAndFilters(Request $request, stdClass $configuration): array
    {
        $sortingSettings = null;
        $sort = null;
        $dir = null;
        if (class_exists('\Pimcore\Bundle\AdminBundle\Helper\QueryParams')) {
            $sortingSettings = \Pimcore\Bundle\AdminBundle\Helper\QueryParams::extractSortingSettings(array_merge($request->request->all(), $request->query->all()));
        }
        if (is_array($sortingSettings) && $sortingSettings['orderKey']) {
            $sort = $sortingSettings['orderKey'];
            $dir = $sortingSettings['order'];
        }
        $filters = ($request->request->has('filter') ? json_decode($request->request->getString('filter'), true) : null);
        $drillDownFilters = $request->request->all('drillDownFilters');
        if ($sort === null && $dir === null && property_exists($configuration, 'orderby') && $configuration->orderby !== '' && $configuration->orderbydir !== '') {
            $sort = $configuration->orderby;
            $dir = $configuration->orderbydir;
        }

        return ['sort' => $sort, 'dir' => $dir, 'filters' => $filters, 'drillDownFilters' => $drillDownFilters];
    }
}
