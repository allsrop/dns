'use strict';
var rdedns = angular.module('rdedns', ['ngMaterial'])
    .config(function($mdThemingProvider){
	$mdThemingProvider.theme('default')
	    .accentPalette('orange');
    });

rdedns.controller('DNSCtrl', function ($scope, $http, $filter, $mdSidenav, $mdDialog, $mdToast) {
    $http.get('d/list', {responseType: 'json'}).success(function (data) {
	$scope.domains = data;
    }).error(function (data) {
	alert('Failed to load data!');
	location.href=location.href;
    });

    $scope.hideNav = function() {
	$mdSidenav('left').close();
    };

    $scope.showNav = function() {
	$mdSidenav('left').open();
    };

    $scope.showDomainForm = function(ev) {
	$mdDialog.show({
	    controller: DomainDialogCtrl,
	    templateUrl: 'addDomain.html',
	    targetEvent: ev
	}).then(function(domain_name) {
	    if (! domain_name) return;

	    var data = {domain: domain_name};
	    $http.post('d/create', data).success(function(data) {
		var msg_part = 'failed: ' + data.message + '.';
		if (data.result) {
		    msg_part = 'added.';
		    $scope.domains.push(data.data);
		}
		var msg = 'Domain ' + domain_name + ' ' + msg_part;
		$mdToast.show(
		    $mdToast.simple().content(msg).position('top right')
		);
	    });
	});
    };

    $scope.showRecordForm = function(ev, domain_obj) {
	$mdDialog.show({
	    locals: {domain: domain_obj},
	    controller: RecordDialogCtrl,
	    templateUrl: 'addRecord.html',
	    targetEvent: ev
	}).then(function(obj) {
	    if (! obj) return;
	    $http.post('r/create', obj).success(function(data) {
		var msg = 'Failed to add record: ' + data.message + '.';
		if (data.result) {
		    msg = 'Record is added.';
		    domain_obj.records.push(data.data);
		}
		$mdToast.show(
		    $mdToast.simple().content(msg).position('top right')
		);
	    });
	});
    };

    $scope.delDomain = function(ev, domain) {
	var confirm = $mdDialog.confirm()
	    .title('Delete domain?')
	    .content('Really delete whole ' + domain.domain + ' domain?')
	    .ok('Delete')
	    .cancel('Cancel')
	    .targetEvent(ev);
	$mdDialog.show(confirm).then(function(){
	    var obj = {id: domain.id};
	    $http.post('d/delete', obj).success(function(data) {
		var msg = 'Failed to delete domain ' + domain.domain + ': ' + data.message;
		if (data.result) {
		    msg = 'Domain ' + domain.domain + ' deleted.';
		    $scope.domains = $filter('filter')($scope.domains, function(v) {
			if (v.id != domain.id) return v;
                    });
		}
		$mdToast.show(
		    $mdToast.simple().content(msg).position('top right')
		);
	    });
	});
    };

    $scope.delRecord = function(ev, domain, record) {
	var confirm = $mdDialog.confirm()
	    .title('Delete record?')
	    .content('Really delete ' + record.type + ' type record of ' + record.source + '?')
	    .ok('Delete')
	    .cancel('Cancel')
	    .targetEvent(ev);
	$mdDialog.show(confirm).then(function(){
	    var obj = {id: record.id};
	    $http.post('r/delete', obj).success(function(data) {
		var msg = 'Failed to delete record: ' + data.message;
		if (data.result) {
		    msg = record.type + ' type record of ' + record.source + ' deleted.';
		    domain.records = $filter('filter')(domain.records, function(v) {
			if (v.id != record.id) return v;
                    });
		}
		$mdToast.show(
		    $mdToast.simple().content(msg).position('top right')
		);
	    });
	});
    };

    var DomainDialogCtrl = function($scope, $mdDialog) {
	$scope.send = function(domain) {
	    $mdDialog.hide(domain);
	};
    };

    var RecordDialogCtrl = function($scope, $mdDialog, domain) {
	$scope.domain = domain;
	$scope.type = 'A';
	$scope.send = function(source, dest, type, id) {
	    $mdDialog.hide({
		domain: id,
		source: source,
		dest: dest,
		type: type
	    });
	};
    };
});
