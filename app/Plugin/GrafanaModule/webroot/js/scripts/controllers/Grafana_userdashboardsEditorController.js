angular.module('openITCOCKPIT')
    .controller('Grafana_userdashboardsEditorController', function($scope, $http, QueryStringService){
        $scope.id = QueryStringService.getCakeId();

        $scope.inputData = {
            data: [
                /*[//row1
                    [//panel1
                        {//metric1 in panel1
                            hostId: 1,
                            serviceId: 2,
                            metric: null,
                            services: null,
                            metrics: null
                        },
                        {//metric2 in panel1
                            hostId: 2,
                            serviceId: 7,
                            metric: null,
                            services: null,
                            metrics: null
                        }
                    ]
                ]*/
            ],
            hosts: []
        };




        $scope.addNewRow = function(){
            $scope.inputData.data.push([[]]); //new row array with empty panel array
        };

        $scope.addNewPanel = function(rowKey){
            //maximum 4 panels per row
            if($scope.inputData.data[rowKey].length < 4){
                $scope.inputData.data[rowKey].push([]);
            }else{
                console.info('maximum panels for this row reached');
            }
        };

        $scope.removePanel = function(rowKey, panelKey){
            $scope.inputData.data[rowKey].splice(panelKey);
        };

        $scope.removeRow = function(rowKey){
            $scope.inputData.data.splice(rowKey, 1);
        };

        $scope.removeMetric = function(rowKey, panelKey, metricKey){
            $scope.inputData.data[rowKey][panelKey].splice(metricKey, 1);
        };

        $scope.addNewMetric = function(rowKey, panelKey){
            $scope.inputData.data[rowKey][panelKey].push({
                hostId: null,
                serviceId: null,
                metricValue: null,
                row:rowKey,
                panel:panelKey,
                services: null,
                metrics: null
            });
        };


        $scope.loadHosts = function(){
            $http.get("/hosts/loadHostsByString.json", {
                params: {
                    'angular': true
                }
            }).then(function(result){
                $scope.inputData.hosts = result.data.hosts;
            }, function errorCallback(result){
                if(result.status === 404){
                    window.location.href = '/angular/not_found';
                }
            });
        };

        $scope.loadServices = function(hostId, rowKey, panelKey, metricKey){
            if(hostId == null){
                return;
            }

            $http.get("/services/loadServicesByHostId.json", {
                params: {
                    'angular': true,
                    'filter[Host.id]': hostId
                }
            }).then(function(result){
                $scope.inputData.data[rowKey][panelKey][metricKey].services = result.data.services;
                $scope.inputData.data[rowKey][panelKey][metricKey]['metric'] = metricKey;
            });
        };

        $scope.loadMetrics = function(uuids, rowKey, panelKey, metricKey){
            if(uuids == null){
                return;
            }

            $http.get("/grafana_module/grafana_userdashboards/loadServiceruleFromService.json", {
                params: {
                    'angular': true,
                    'hostUuid': uuids.hostUuid,
                    'serviceUuid': uuids.serviceUuid
                }
            }).then(function(result){
                $scope.inputData.data[rowKey][panelKey][metricKey].metrics = result.data.perfdataStructure;
            });
        };

        $scope.getUuids = function(serviceId, rowKey, panelKey, metricKey){
            var services = $scope.inputData.data[rowKey][panelKey][metricKey].services;
            var ret = {};
            angular.forEach(services, function(v){
                var currentEntry = v.value;
                if(currentEntry.Service.id == serviceId){
                    ret = {
                        serviceUuid: currentEntry.Service.uuid,
                        hostUuid: currentEntry.Host.uuid
                    };
                    return ret;
                }
            });
            return ret;
        };


        $scope.hostSelected = function(hostId, rowKey, panelKey, metricKey){
            $scope.loadServices(hostId, rowKey, panelKey, metricKey);
        };


        $scope.serviceSelected = function(serviceId, rowKey, panelKey, metricKey){
            var uuids = $scope.getUuids(serviceId, rowKey, panelKey, metricKey);
            $scope.loadMetrics(uuids, rowKey, panelKey, metricKey);
        };


        $scope.metricSelected = function(rowKey, panelKey, metricKey){
            /*  console.log('row ' + rowKey);
              console.log('panel ' + panelKey);
              console.log('metric ' + metricKey);
  */
            var newData = $scope.cleanupData($scope.inputData.data);
            $scope.saveData(newData);
        };

        $scope.saveData = function(dataToSave){
            if(dataToSave != null && dataToSave.length > 0){
                $http.post("/grafana_module/grafana_userdashboards/editor/" + $scope.id + ".json?angular=true",
                    dataToSave
                ).then(function(result){

                    //window.location.href = '/tenants/index';
                }, function errorCallback(result){
                    if(result.data.hasOwnProperty('error')){
                        $scope.errors = result.data.error;
                    }
                });
            }

        };


        /**
         * removes all unessescary data from the inputData.data array like the available services from the chosen host
         * or the available metrics of the chosen service
         */
        $scope.cleanupData = function(data){
            var filteredInputData = [];
            for(var r in data){
                if(Array.isArray(data[r])){
                    filteredInputData[r] = [];
                    for(var p in data[r]){
                        filteredInputData[r][p] = [];
                        for(var m in data[r][p]){
                            var currentData = data[r][p][m];
                            if(currentData.hasOwnProperty('metricValue')){
                                console.log(currentData);
                                var dataToSave = {
                                    host_id: currentData['hostId'],
                                    service_id: currentData['serviceId'],
                                    metric_value: currentData['metricValue'],
                                    row:currentData['row'],
                                    panel:currentData['panel'],
                                    metric: currentData['metric']
                                };

                                filteredInputData[r][p][m] = dataToSave;
                            }
                        }
                    }
                }
            }

            return filteredInputData;
/*
        $scope.inputData = {
            data: [
                [//row1
                    [//panel1
                        {//metric1 in panel1
                            hostId: 1,
                            serviceId: 2,
                            metric: null,
                            services: null,
                            metrics: null
                        },
                        {//metric2 in panel1
                            hostId: 2,
                            serviceId: 7,
                            metric: null,
                            services: null,
                            metrics: null
                        }
                    ]
                ]
            ],
            hosts: []
        };
 */


        };

        $scope.$watch('errors', function(){
            console.log($scope.errors);
        });
        $scope.loadHosts();

    });