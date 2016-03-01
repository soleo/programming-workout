<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Book Finder</title>
        <style type="text/css">
        /**
         * Hide when Angular is not yet loaded and initialized
         */
        [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak {
          display: none !important;
        }
        </style>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="">
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no" />
        <link href="//fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

        <!-- Angular Material style sheet -->
        <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/angular_material/1.0.0/angular-material.min.css">
        </head>


        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
            }
            .current-price {
              font-size: 80px;
              font-weight: 700;
              color:#e74c3c;
              margin-top: 0;
            }
        </style>
    </head>
    <body ng-app="BookFinderApp" ng-cloak layout="row">
        <div class="container">
            <div class="content" ng-controller="BookController">
                <div class="title">Book Finder</div>
                <p class="lead">
                  Search by multiple ISBN seperated by comma or single ISBN.<br/>
                  e.g. <kbd>0-8044-2957-X</kbd> , <kbd>9780321193858,9780961584146</kbd> , <kbd>9780321193858</kbd>
                </p>
                <md-content layout-padding>
                    <div>
                      <md-input-container class="md-block">
                        <label>Search By ISBN</label>
                        <input ng-model="query" ng-keyup="search()">
                      </md-input-container>
                      <md-button ng-click="search()" class="md-raised md-primary">Search</md-button>
                    </div>
                </md-content>

                <search-result>
                  <no-result ng-if="showAPIError">
                    <md-toolbar class="md-warn">
                        <div class="md-toolbar-tools">
                          <h2 class="md-flex" ng-repeat="error in errors">
                              {{ error }}
                          </h2>
                        </div>
                    </md-toolbar>
                    <md-content flex layout-padding>
                    </md-content>
                  </no-result>
                  <md-content flex layout-padding ng-if="!showAPIError">
                    <div ng-repeat="book in books" layout-gt-sm="column" layout-padding flex="33">
                  <md-card>
                    <md-card-title>
                      <md-card-title-text>
                        <span class="md-headline">Book Price</span>
                      </md-card-title-text>
                    </md-card-title>
                    <md-card-content>
                        <md-content layout-padding flex>
                        <h1 class="current-price"> {{ book.price | currency }}</h1>
                        <table class="table table-hover table-stripped">
                          <tbody>
                            <tr>
                              <td class="text-right"> Shipping Price</td>
                              <td> <strong>{{ book.shipping_price | currency }}</strong> </td>
                            </tr>
                            <tr>
                              <td class="text-right"> Term</td>
                              <td> <strong class="text-capitalize">{{ book.term }}</strong> </td>
                            </tr>
                            <tr>
                              <td class="text-right"> Book Condition</td>
                              <td> <strong class="text-capitalize">{{ book.condition }}</strong> </td>
                            </tr>
                            <tr>
                              <td class="text-right"> ISBN </td>
                              <td> <strong>{{ book.isbn13 }}</strong> </td>
                            </tr>

                          </tbody>
                        </table>
                    <p>
                    source from {{ book.retailer }}
                    </p>
                    </md-card-content>

                    <md-card-actions layout="column" layout-align="start">
                      <md-button href="{{ book.url }}" class="md-raised md-primary">Rent/Buy Now</md-button>
                    </md-card-actions>
                  </md-card>
                  </div>
                  </md-content>
                </search-result>
            </div>
        </div>

        <!-- Angular Material requires Angular.js Libraries -->
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular-animate.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular-aria.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular-messages.min.js"></script>

        <!-- Angular Material Library -->
        <script src="//ajax.googleapis.com/ajax/libs/angular_material/1.0.0/angular-material.min.js"></script>


        <script type="text/javascript">
        (function(){
          var app = angular.module('BookFinderApp', ['ngMaterial']);
          app.controller('BookController', ['$scope', '$http', function($scope, $http){
              $scope.search = function () {

                var q = $scope.query;

                $http.get('book/search?q='+q).success(function(data) {
                  console.log(data);
                  if( typeof data.errors !== 'undefined' ) {
                    $scope.showAPIError = true;
                    $scope.errors = data.errors;
                  } else {
                    $scope.showAPIError = false;
                    $scope.books = data;
                    delete $scope.errors;
                  }
                });
              }
          }]);

         })();
        </script>
    </body>
</html>
