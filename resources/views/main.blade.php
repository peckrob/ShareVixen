@extends('layouts/app')

@section('app-controller', "files")

@section('content')
<script type="text/ng-template" id="nodes_renderer.html">
    <div ng-if="node.nodes" ui-tree-handle class="tree-node tree-node-content" style="padding-left: @{{node.indent_level*30}}px">
        <i class="fa fa-folder" aria-hidden="true"></i> <a ng-click="loadChildren(node, this)">
            @{{node.title}}
            <i ng-show="loading" class="fa fa-spinner fa-spin fa-fw"></i>
        </a>
    </div>
    <div ng-if="!node.nodes" class="tree-node">
        <div class="row">
            <div class="col-lg-8 col-md-6 col-sm-6" style="padding-left: @{{node.indent_level*30}}px">
                <i class="fa fa-file" aria-hidden="true"></i> <a href="@{{node.url}}">@{{node.title}}</a>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 hidden-xs">
                @{{node.size | filesize}}
            </div>
            <div class="col-lg-2 col-md-2 col-sm-4 hidden-xs">
                @{{node.date.date | amUtc | amDateFormat:'YYYY-MM-DD HH:mm:ss'}}
            </div>
        </div>
    </div>
    <ol ui-tree-nodes="" ng-model="node.nodes"  ng-class="{hidden: collapsed}">
        <li ng-repeat="node in node.nodes" ui-tree-node ng-include="'nodes_renderer.html'"></li>
    </ol>
</script>

<div class="container-fluid">
    <div ng-cloak>
        <div ui-tree data-nodrop-enabled="true" data-drag-enabled="false" data-clone-enabled="false">
            <ol ui-tree-nodes="" ng-model="files" id="tree-root">
                <li ng-repeat="node in files" ui-tree-node ng-include="'nodes_renderer.html'"></li>
          </ol>
      </div>
    </div>
</div>
@endsection
