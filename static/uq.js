/**
 * Created by Михаил on 15.10.2018.
 */
function __UQ($) {
  this.arCss = [];
  this.arJs = [];
  this.arHTML = [];
  this.arScript = [];
  this.insertStatus = {};

  this.addCss = function (src) {
    var index = this.arCss.length;
    this.arCss[index] = src;
  };

  this.addJs = function (src) {
    var index = this.arJs.length;
    this.arJs[index] = src;
  };


  this.addHTML = function (src, contain) {
    this.arHTML[contain] = src;
  };


  this.addScript = function (content) {
    var index = this.arScript.length;
    this.arScript[index] = content;
  };


  this.insertElements = function () {
    for (var i in this.arCss) {
      $("head").append("<link rel='stylesheet' type='text/css' href='" + this.arCss[i] + "' />");
    }
    this.insertStatus.css = true;

    for (i in this.arJs) {
      $("body").append("<script type='text/javascript' src='" + this.arJs[i] + "'></script>");
    }
    this.insertStatus.js = true;

    for (i in this.arScript) {
      $("body").append("<script type='text/javascript'>" + this.arScript[i] + "</script>");
    }
    this.insertStatus.script = true;

    for (i in this.arHTML) {
      $(i).append(this.arHTML[i]);
    }
    this.insertStatus.html = true;

    this.afterImage = function () {
      $('*[data-after-img="true"]').each(function () {
        var url = $(this).attr("data-after-url");
        $(this).attr("src", url);
      });
    }


  };

}

function imgSprite($) {
  $('*[data-img-sprite="true"]').each(function () {
    var url = $(this).attr("data-img-url");
    var width = $(this).attr("data-sprite-width") - 0;
    var height = $(this).attr("data-sprite-height") - 0;
    var host = $(this).attr("data-sprite-host");
    var x = $(this).attr("data-sprite-x") - 0;
    var y = $(this).attr("data-sprite-y") - 0;


    var iPlaceW = $(this).attr("data-sprite-space-w") - 0;
    var iPlaceH = $(this).attr("data-sprite-space-h") - 0;
    var realElemW = $(this).width() - 0;


    var obPercents = {
      width: (width / 100),
      height: (height / 100),
      posSpriteX: (x / 100),
      posSpriteY: (y / 100),
      widthSprite: (iPlaceW / 100),
      heightSprite: (iPlaceH / 100),
      ratio: 100
    };

    var p = obPercents.ratio;
    if (host) {
      url = "//" + host + url;
    }

    if (realElemW < width && realElemW > 50) {
      p = Math.ceil(obPercents.ratio = (realElemW / obPercents.width));
    }
    $(this).css({
      "background": "url(" + url + ") no-repeat " + 0 + "px -" + (obPercents.posSpriteY * p) + "px",
      "backgroundSize": (obPercents.widthSprite * p) + 'px ' + (obPercents.heightSprite * p) + "px",
      "height": (obPercents.height * p)+"px"
    });
  });
}

var UQ = new __UQ(jQuery);
UQ.addCss("/fonts.css-n");
UQ.addJs("//aflt.market.yandex.ru/widget/script/api");
UQ.addJs("//cse.google.com/cse.js?cx=003064882461470848978:t0r0skrwadg");

window.onload = function () {
  UQ.insertElements();
  UQ.afterImage();
  imgSprite(jQuery);
};

jQuery(window).resize(function () {
  imgSprite(jQuery);
});