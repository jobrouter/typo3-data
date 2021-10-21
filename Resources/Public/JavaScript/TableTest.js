define([
  'TYPO3/CMS/Core/DocumentService',
  'TYPO3/CMS/Core/Event/RegularEvent',
  'TYPO3/CMS/Core/Ajax/AjaxRequest',
  'TYPO3/CMS/Backend/Notification'
], (DocumentService, RegularEvent, AjaxRequest, Notification) => {
  'use strict';

  const tableCheck = (id, name) => {
    const notificationTitle = TYPO3.lang['table_check_for'] + ' ' + name;
    const request = new AjaxRequest(TYPO3.settings.ajaxUrls['jobrouter_data_table_test']);
console.log(TYPO3.settings.ajaxUrls['jobrouter_data_table_test']);
    request.post({tableId: +id}).then(
      async response => {
        const data = await response.resolve();
        if (data.check && data.check === 'ok') {
          Notification.success(notificationTitle, TYPO3.lang['table_check_successful'], 5);
          return;
        }

        if (data.error) {
          Notification.error(notificationTitle, data.error);
          return;
        }

        Notification.error(notificationTitle, TYPO3.lang['table_check_unknown_error']);
      }, error => {
        Notification.error(notificationTitle, TYPO3.lang['table_check_unknown_error'] + ' (' + error.statusText + ', ' + error.status + ')');
      }
    );
  }

  DocumentService.ready().then(() => {
    const tableListElement = document.getElementById('jobrouter-data-table-list');

    if (!tableListElement) {
      return;
    }

    new RegularEvent('click', event => {
      const linkElement = event.target.closest('.jobrouter-data-table-check');

      if (!linkElement) {
        return;
      }

      event.preventDefault();
      tableCheck(linkElement.dataset.tableUid, linkElement.dataset.tableName);
    }).bindTo(tableListElement);
  });
});
