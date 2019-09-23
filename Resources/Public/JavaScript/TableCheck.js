define(['jquery'], function($) {
  'use strict';

  var TableCheck = {};

  TableCheck.init = function() {
    window.addEventListener('load', function() {
      var listElement = document.getElementById('jobrouter-data-table-list');

      if (!listElement) {
        return;
      }

      listElement.addEventListener('click', function(event) {
        var linkElement = event.target.closest('.jobrouter-data-table-check');

        if (!linkElement) {
          return;
        }

        event.preventDefault();

        TableCheck.check(
          linkElement.dataset.tableUid,
          linkElement.dataset.tableName
        );
      });
    });
  };

  TableCheck.check = function(id, name) {
    var url = top.TYPO3.settings.ajaxUrls['jobrouter_data_table_check'];
    var settings = {
      type: 'POST',
      data: {tableId: +id}
    };

    var notificationTitle = TYPO3.lang['table_check_for'] + ' ' + name;
    $.ajax(url, settings)
      .done(function(data) {
        if (data.check === 'ok') {
          top.TYPO3.Notification.success(notificationTitle, TYPO3.lang['table_check_successful'], 5);
          return;
        }

        if (data.error) {
          top.TYPO3.Notification.error(notificationTitle, data.error);
          return;
        }

        top.TYPO3.Notification.error(notificationTitle, 'Unknown error');
      })
      .fail(function(jqXhr, textStatus) {
        top.TYPO3.Notification.error(notificationTitle, 'Unknown error (' + textStatus + ', ' + jqXhr.status + ')');
      });
  };

  TableCheck.init();
});
