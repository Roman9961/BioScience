import React from 'react';
import ReactDOM from 'react-dom';
import {withFormik, Form} from "formik";
import axios from 'axios';
import qs from 'qs';
import Select from 'react-select';
import * as Yup from 'yup';
import jquery from  'jquery';
require('bootstrap');


const FormStepFive = () => (
    <div className="formik-container">
         <div className="form-step-5">
             <h3 className="text-center py-3"><b>Thank you for your feedback!</b></h3>
             <hr/>
             <div className="text-center pt-3">We appreciate you taking the time to share your experience with us. You will receive a confirmation email as soon as your free bottle has shipped! Please allow 1-2 weeks for delivery.</div>
         </div>
    </div>
);

export default FormStepFive;