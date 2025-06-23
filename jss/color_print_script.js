// Code goes here


    function setColors(selector) {
      var elements = $(selector);
      for (var i = 0; i < elements.length; i++) {
        var eltBackground = $(elements[i]).css('background-color');
        var eltColor = $(elements[i]).css('color');

        var elementStyle = elements[i].style;
        if (eltBackground) {
          elementStyle.oldBackgroundColor = {
            value: elementStyle.backgroundColor,
            importance: elementStyle.getPropertyPriority('background-color'),
          };
          elementStyle.setProperty('background-color', eltBackground, 'important');
        }
        if (eltColor) {
          elementStyle.oldColor = {
            value: elementStyle.color,
            importance: elementStyle.getPropertyPriority('color'),
          };
          elementStyle.setProperty('color', eltColor, 'important');
        }
      }
    }

    function resetColors(selector) {
      var elements = $(selector);
      for (var i = 0; i < elements.length; i++) {
        var elementStyle = elements[i].style;

        if (elementStyle.oldBackgroundColor) {
          elementStyle.setProperty('background-color', elementStyle.oldBackgroundColor.value, elementStyle.oldBackgroundColor.importance);
          delete elementStyle.oldBackgroundColor;
        } else {
          elementStyle.setProperty('background-color', '', '');
        }
        if (elementStyle.oldColor) {
          elementStyle.setProperty('color', elementStyle.oldColor.value, elementStyle.oldColor.importance);
          delete elementStyle.oldColor;
        } else {
          elementStyle.setProperty('color', '', '');
        }
      }
    }

    function setIconColors(icons) {
      var css = '';
      $(icons).each(function () {
        var path, node = $(this);
        while (node.length) {
            var realNode = node[0], name = realNode.localName;
            if (!name) break;
            name = name.toLowerCase();

            var parent = node.parent();

            var sameTagSiblings = parent.children(name);
            if (sameTagSiblings.length > 1) { 
                allSiblings = parent.children();
                var index = allSiblings.index(realNode) + 1;
                if (index > 1) {
                    name += ':nth-child(' + index + ')';
                }
            }

            path = name + (path ? '>' + path : '');
            node = parent;
        }
        css += path + ':before { color: ' + $(this).css('color') + ' !important; }';
      });
      $('head').append('<style id="print-icons-style">' + css + '</style>');
    }

    function resetIconColors() {
      $('#print-icons-style').remove();
    }
