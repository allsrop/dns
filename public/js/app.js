'use strict';
var rdedns = angular.module('rdedns', []);

rdedns.controller('DNSCtrl', function ($scope, $http, $filter) {
    $http.get('d/list', {responseType: 'json'}).success(function (data) {
	$scope.domains = data;
    }).error(function (data) {
	alert('Failed to load data!');
	location.href=location.href;
    });

    $scope.delDomain = function(domain) {
	var id = domain.id;
	$http.post('d/delete', {id: domain.id}).success(function (data) {
	    if (data.result) {
		domain.disabled = true;
		$scope.domains = $filter('filter')($scope.domains, function(v) {
		    if (v.id != domain.id) return v;
		});
	    } else {
		alert(data.message);
	    }
	}).error(function (data) {
	    console.log(data);
	});
    };

    $scope.delRecord = function(record) {
	alert('TODO: really delete record ' + record.source);
    };

    $scope.showDomainForm = function() {
	$scope.domain_show = true;
    };
    $scope.hideDomainForm = function() {
	$scope.domain_show = false;
    };
    $scope.doAddDomain = function(name) {
	$http.post('d/create', {domain:name}).success(function(data) {
	    if (data.result) {
		$scope.domains.push(data.data);
		$scope.new_domain_name = '';
	    }
	});
	$scope.hideDomainForm();
    };
    $scope.showRecordForm = function(domain) {
	domain.record_show = true;
	domain.new_record_type = 'A';
    };
    $scope.hideRecordForm = function(domain) {
	domain.record_show = false;
    };
    $scope.doAddRecord = function(domain) {
	var data = {
	    domain: domain.id,
	    source: domain.new_record_source,
	    dest:   domain.new_record_target,
	    type:   domain.new_record_type
	};
	$http.post('r/create', data).success(function(data) {
	    if (data.result) {
		domain.records.push(data.data);
		domain.new_record_type = 'A';
		domain.new_record_source = null;
		domain.new_record_target = null;
		$scope.hideRecordForm(domain);
	    } else {
		alert(data.message);
	    }
	});
    };
    $scope.delRecord = function(domain, record) {
	var d = {id: record.id};
	$http.post('r/delete', d).success(function(data) {
	    if (data.result) {
		domain.records = $filter('filter')(domain.records, function(v) {
		    if (v.id != record.id) return v;
		});
	    }
	});
    };
});
