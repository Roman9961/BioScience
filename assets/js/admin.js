import 'bootstrap'
import '../css/admin.scss'
import 'startbootstrap-sb-admin-2/js/sb-admin-2.min'
import 'bootstrap-table'
import 'tableexport.jquery.plugin'
import 'bootstrap-daterangepicker'
import './drp'
import 'bootstrap-table/dist/extensions/export/bootstrap-table-export'
import axios from 'axios'
import qs from 'qs'
import moment from 'moment'

import $ from 'jquery'


const selectGradeLimit = document.getElementById("grade_limit");

selectGradeLimit.addEventListener("change", (e)=>{
   const gradeLimit = e.currentTarget.value

    axios.post('/admin/set_grade_limit', qs.stringify({gradeLimit}), {
        headers: {'content-type': 'application/x-www-form-urlencoded'},
    })
        .then(function (response) {

        })
        .catch(function (error) {
            console.log(error.response);
        });
})

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
        onClickCell: function (field, value, row, $element) {
            if(field === 'isSended'){
                let question = row.isSended?'Mark as not sended?':'Mark as sended?'
                if (confirm(question)) {
                    let data = {customer: row.id}
                    axios.post('/admin/is_send', qs.stringify(data), {
                        headers: {'content-type': 'application/x-www-form-urlencoded'},
                    })
                        .then(function (response) {
                            $table.bootstrapTable('refresh');
                        })
                        .catch(function (error) {
                            console.log(error.response);
                        });
                }
            }
        },
        columns: [{
            field: 'orderId',
            title: 'Order ID',
            sortable:true,
            filterControl: 'input'
        }, {
            field: 'shippingInfo',
            title: 'Shipping Info',
            formatter: (value)=> (
                `
                <div>
                 <b>Customer:</b> ${value.name?value.name:''}
                </div>
                <div>
                 <b>Apt:</b> ${value.apt?value.apt:'-'}
                </div>
                <div>
                 <b>Street:</b> ${value.street?value.street:'-'}
                </div>
                <div>
                 <b>City:</b> ${value.city?value.city:'-'}
                </div>
                <div>
                 <b>State:</b> ${value.state?value.state:'-'}
                </div>
                <div>
                 <b>Zip code:</b> ${value.zipCode?value.zipCode:'-'}
                </div>
            `
            ),
        }, {
            field: 'email',
            title: 'Email',
            formatter: (value)=> value?value:'-',
            sortable:true
        }, {
            field: 'product',
                title: 'Product',
                formatter: (value)=> value.map(product => product.name),
            sortable:true
        },{
            field: 'orderDate',
            title: 'Order Date',
            filterControl: 'datepicker',
            filterDatepickerOptions:{
                autoApply:true,
                autoUpdateInput:true,
                todayHighlight: true,
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
            formatter: (value)=> moment(value).format('Y-MM-DD HH:mm:ss')
        },{
            field: 'feedbackDate',
            title: 'Feedback Date',
            filterControl: 'datepicker',
            filterDatepickerOptions:{
                autoApply:true,
                autoUpdateInput:true,
                todayHighlight: true,
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
            formatter: (value)=> moment(value)._isValid?moment(value).format('Y-MM-DD HH:mm:ss'):'-'
        }, {
            field: 'feedback',
            title: 'Feedback',
            width: 200
        }, {
            field: 'grade',
            title: 'Mark',
            sortable:true,
            width: 100,
            formatter: (value)=> {

                let str ='<div class="d-flex">';
                for (let i=0; i<5; i++){
                    if(value && i<value) {
                        str += '<span class="fa fa-star checked"></span>'
                    }else{
                        str += '<span class="fa fa-star"></span>'
                    }
                }
                str+=`<input type="hidden" value="${value?value:'-'}"></div>`
                return str
            }
        },{
            field: 'isRequested',
            title: 'Is Requested',
            filterControl: 'select',
            filterData: 'json:{"1":"requested", "0":"not requested"}',
            sortable:true,
            formatter: (value)=> {
                let button = value?'<button type="button" class="btn btn-success btn-circle"><i class="fa fa-check"></i></button>':'<button type="button" class="btn btn-secondary btn-circle"><i class="fa fa-times"></i></button>'
                return `<div class="d-flex justify-content-center w-100">${button}</div>`
            }
        },{
            field: 'isSended',
            title: 'Is Sended',
            filterControl: 'select',
            filterData: 'json:{"1":"sended", "0":"not sended"}',
            sortable:true,
            formatter: (value)=> {
                let button = value?'<button type="button" class="btn btn-success btn-circle"><i class="fa fa-check"></i></button>':'<button type="button" class="btn btn-secondary btn-circle"><i class="fa fa-times"></i></button>'
                return `<div class="d-flex justify-content-center w-100">${button}</div>`
            }
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