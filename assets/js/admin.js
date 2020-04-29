import 'bootstrap'
import '../css/admin.scss'
import 'startbootstrap-sb-admin-2/js/sb-admin-2.min'
import 'bootstrap-table'
import 'tableexport.jquery.plugin'
import 'bootstrap-daterangepicker'
import './drp'
import 'bootstrap-table/dist/extensions/export/bootstrap-table-export'
import axios from 'axios'
import moment from 'moment'

import $ from 'jquery'



// import 'bootstrap-select'
document.addEventListener("DOMContentLoaded", ()=>{
    const $table =  $('.table');

    $table.bootstrapTable({
        url: '/admin/data',
        dataType: 'json',
        showExport:true,
        sortable:true,
        filterControl:true,
        exportDataType: 'all',
        pagination:true,
        sidePagination:'server',
        filterShowClear:true,
        exportOptions: {
            fileName: function () {
                return 'exportName'
            }
        },
        columns: [{
            field: 'orderId',
            title: 'Order ID',
            sortable:true
        }, {
            field: 'shippingInfo',
            title: 'Shipping Info',
            formatter: (value)=> (
                `
                <div>
                 <b>Customer:</b> ${value.name?value.name:''}
                </div>
                <div>
                 <b>State:</b> ${value.state}
                </div>
                <div>
                 <b>City:</b> ${value.city}
                </div>
                <div>
                 <b>Street:</b> ${value.street}
                </div>
                <div>
                 <b>Apt:</b> ${value.apt}
                </div>
                <div>
                 <b>Zip:</b> ${value.zipCode}
                </div>
            `
            ),
        }, {
            field: 'orderDate',
            title: 'Order Date',
            filterControl: 'datepicker',
            filterDatepickerOptions:{
                autoApply:true,
                autoUpdateInput:true,
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges:{
                    'Clear': '',
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'This Week': [moment().startOf('week'), moment()],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            },
            sortable:true,
            formatter: (value)=> moment(value).format('Y-MM-D HH:mm:ss')
        },{
            field: 'feedbackDate',
            title: 'Feedback Date',
            filterControl: 'datepicker',
            filterDatepickerOptions:{
                autoApply:true,
                autoUpdateInput:true,
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges:{
                    'Clear': '',
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'This Week': [moment().startOf('week'), moment()],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            },
            sortable:true,
            icons:{
                clear: 'glyphicon-trash icon-clear'}
            ,
            formatter: (value)=> moment(value)._isValid?moment(value).format('Y-MM-D HH:mm:ss'):'-'
        },{
            field: 'grade',
            title: 'Mark',
            sortable:true,
            formatter: (value)=> {

                let str ='';
                for (let i=0; i<5; i++){
                    if(value && i<value) {
                        str += '<span class="fa fa-star checked"></span>'
                    }else{
                        str += '<span class="fa fa-star"></span>'
                    }
                }
                str+=`<input type="hidden" value="${value?value:'-'}">`
                return str
            }
        },{
            field: 'isSended',
            title: 'Is Sended',
            filterControl: 'select',
            filterData: 'json:{"1":"sended", "0":"not sended"}',
            sortable:true,
            formatter: (value)=> +value
        }]
    })
    $('.date-filter-control').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $(this).trigger('change');
    });
    $('.date-filter-control').on('apply.daterangepicker', function(ev, picker) {
        if(picker.chosenLabel=='Clear'){
            picker.element.trigger('cancel.daterangepicker');
        }
    });


});