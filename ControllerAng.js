angular.module('starter.controllers', [])

.controller('EmergenciasCtrl', function($scope, TelefonosEmergencias) {
  $scope.telefonos = TelefonosEmergencias.all();
})

.controller('NoticiasCtrl', 
[
  '$ionicPlatform', 
  '$scope', 
  '$rootScope',
  '$ionicActionSheet', 
  '$timeout',
  '$ionicLoading', 
  '$state', 
  'rssService', 
  'settings',
  '$ionicSlideBoxDelegate',
  '$cordovaSocialSharing',
  '$cordovaNetwork',
  function(
    $ionicPlatform, 
    $scope, 
    $rootScope,
    $ionicActionSheet,
    $timeout,
    $ionicLoading, 
    $state, 
    rssService, 
    settings,
    $ionicSlideBoxDelegate,
    $cordovaSocialSharing,
    $cordovaNetwork
  ) {

    $scope.contador = 0;
    $scope.noMoreItemsAvailable = false;

    $rootScope.appOnline = true;

    

    $ionicLoading.show({
      template: 'Cargando noticias ...'
    });

    $ionicPlatform.ready(function() {

      console.log("Started up!!");

      document.addEventListener("deviceready", function () {

        if ( $cordovaNetwork.isOffline() ){
          $rootScope.$apply(function(){
            $rootScope.appOnline = false;
          })
        }

        // listen for Online event
        $rootScope.$on('$cordovaNetwork:online', function(event, networkState){
          $rootScope.$apply(function(){
            $rootScope.appOnline = true;
          })
        })

        // listen for Offline event
        $rootScope.$on('$cordovaNetwork:offline', function(event, networkState){
          $rootScope.$apply(function(){
            $rootScope.appOnline = false;
          })
        })

      }, false);

      rssService.getEntries(settings.rss).then(function(entries) {
        nomOfFeatures = 5;
        $scope.feed = {
          noticias : entries.slice(nomOfFeatures),
          title    : settings.title,
          avatar   : settings.img,
          features : entries.slice(0, nomOfFeatures)
        };
        $scope.$apply();
        $ionicSlideBoxDelegate.update();
        $ionicLoading.hide();
      });

    });

    $scope.doRefresh = function(){
      rssService.getEntries(settings.rss).then(function(entries) {
        
        nomOfFeatures = 5;

        $scope.feed = {
          noticias : entries.slice(nomOfFeatures),
          title   : settings.title,
          avatar  : settings.img,
          features : entries.slice(0, nomOfFeatures)
        };
        $scope.$apply();
        $ionicSlideBoxDelegate.update();
      });

      // Stop the ion-refresher from spinning
      $scope.$broadcast('scroll.refreshComplete');
    };

    $scope.loadMoreData = function() {
      $timeout(function() {
        $scope.contador += 10;
        console.log($scope.contador);
        if ( $scope.contador >= $scope.feed.noticias.length ) {
          $scope.noMoreItemsAvailable = true;
        }
        $scope.$broadcast('scroll.infiniteScrollComplete');
      }, 1000);
      
    };
    $scope.$on('$stateChangeSuccess', function() {
      $scope.loadMoreData();
    });

    $scope.fecha = function(d){
      var fec = new Date(d);
      return fec;
    }

    $scope.getThumb = function(input){

      var myRegex = /<img\s+[^>]*\bsrc\s*\=\s*[\x27\x22](.+\.jpg)[\x27\x22]/;
      var out = myRegex.exec(input);
      if (out == null) {
        return $scope.feed.avatar;
      }else{
        return out[1];
      }
    };
    
    $scope.openInAppBrowser = function(enlace)
    {
      // Open in app browser
      window.open(enlace, '_self'); 
    };

    // Triggered on a button click, or some other target  mensaje, imgNota
    $scope.show = function(itemUrl, itemTitle) {

      window.plugins.socialsharing.shareWithOptions(
        {
          message: '#ANGApp: ' + itemTitle,
          subject: 'Agencia de Noticias Guerrero',
          files: null,
          url: decodeURIComponent(itemUrl),
          chooserTitle: 'Elija una aplicación'
        }, 
        function(result) {
          console.log("Share completed? " + result.completed);
          console.log("Shared to app: " + result.app);
          alert(result);
        }, 
        function(msg) {
          console.log("Sharing failed with message: " + msg);
          return false;
        }
      );

    };

}])


.controller('AcercadeCtrl', function($scope, DataService, FuentesService) {

  $scope.fuentesList = FuentesService.all();
  $scope.data = {
    noticiaSide: 'Agencia de Noticias Guerrero'
  };

  $scope.noticiaSideChange = function(item) {
    DataService.data.activo = item.id;
  };

  $scope.pushNotificationChange = function() {
    console.log('Push Notification Change', $scope.pushNotification.checked);
  };
  $scope.pushNotification = { checked: true };
  $scope.emailNotification = 'Subscribed';

})

.controller('NoticiaDetailCtrl', function($scope, $stateParams, FeedService) {
  $scope.not = FeedService.get($stateParams.noticiaObj);
  console.log($stateParams.noticiaObj);
  console.log( FeedService.all() );
})

.filter('clean', function() {
  return function(input) {
    var myRegex = /<img\s+[^>]*\bsrc\s*\=\s*[\x27\x22](.+\.jpg)[\x27\x22]/;
    var out = myRegex.exec(input);

    console.log(out);
    return out[1];
  };
});
