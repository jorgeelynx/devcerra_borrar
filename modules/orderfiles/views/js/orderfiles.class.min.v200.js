/**
 * orderfiles Prestashop module
 *
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Wiktor Koźmiński
 * @copyright 2017-2017 Silver Rose Wiktor Koźmiński
 * @license   LICENSE.txt
 */
function OrderFiles(a){function b(){d.length&&(16==g?d.modal():d.show())}var c=$(".orderfiles-icon-holder"),d=$("#orderfiles_panel"),e="",f=null,g=16;this.openPanel=b,function(a){a.toolbar&&a.toolbar.prepend(c.contents()),a.apiUrl&&(e=a.apiUrl),g=a.psVersion?a.psVersion:16,a.orderId&&(f=a.orderId)}(a)}