@extends('layouts/app')

@section('app-controller', 'users')

@section('content')
<div class="container-fluid">
    <table class="table" ng-cloak>
        <thead>
            <tr>
                <th>
                    Name
                </th>
                <th>
                    Email
                </th>
                <th class="text-center">
                    Approved
                </th>
                <th class="text-center">
                    Can Upload
                </th>
                <th class="text-center">
                    Can Modify
                </th>
                <th class="text-center">
                    Can Admin
                </th>
                <th>

                </th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="user in users">
                <td>
                    @{{ user.name }}
                </td>
                <td>
                    @{{ user.email }}
                </td>
                <td class="text-center">
                    <input type="checkbox" ng-model="user.approved" ng-true-value="1" ng-false-value="0" ng-change="user.$save()" />
                </td>
                <td class="text-center">
                    <input type="checkbox" ng-model="user.can_upload" ng-true-value="1" ng-false-value="0" ng-change="user.$save()" />
                </td>
                <td class="text-center">
                    <input type="checkbox" ng-model="user.can_manage" ng-true-value="1" ng-false-value="0" ng-change="user.$save()" />
                </td>
                <td class="text-center">
                    <input type="checkbox" ng-model="user.can_admin" ng-true-value="1" ng-false-value="0" ng-change="user.$save()" />
                </td>
                <td>
                    <button class="btn btn-xs btn-danger" ng-click="destroy(user)">Delete</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
