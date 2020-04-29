import React from 'react';
import ReactDOM from 'react-dom';
import {withFormik, Form} from "formik";
import axios from 'axios';
import qs from 'qs';
import Select from 'react-select';
import * as Yup from 'yup';
import jquery from  'jquery';
require('bootstrap');


const FormStepThree = ({
    customer,
    handleStep,
    amazonFeedback,
    handleAmazonFeedback
 }) => (
    <div className="formik-container w-100">
     <React.Fragment>
         <div className="form-progress-bar"><div className="form-progress form-progress-w60"><b>60% Completed</b></div></div>
         <div className="form-step-3">
         <h3 className="text-center py-3"><b>We're glad you're enjoying our product!</b></h3>
             <div className="text-center pt-3"><small><b>Please consider leaving your honest, unbiased review on Amazon</b></small></div>
         <div className="text-center pt-3"><small>It will just take a moment to share your experience, and while it's not mandatory, your feedback can help others just like you find our great products!</small></div>
                <div className="d-flex justify-content-around align-items-center pt-3">
                    <div className="d-flex flex-column align-items-center">
                        <div><small><b>Step 1 - COPY TEXT BELOW:</b></small></div>
                        <textarea className="form-control review" placeholder = "Must be a minimum of 50 characters" name="feedback" value={customer.feedback} onChange={()=>customer.feedback}/>
                        <button type="submit" className="btn btn-success text-uppercase font-weight-bold mt-2" onClick={(event)=>{
                            const element =jquery(event.target);
                            element.popover({content:'Copied', delay: { "show": 300, "hide": 300 },placement:"bottom"}).popover('show');
                            setTimeout(()=>{
                                element.popover('hide');
                            },1000);

                            navigator.clipboard.writeText(customer.feedback)}

                        }><small><b>Copy Review</b></small></button>
                    </div>
                    <div className="d-flex flex-column align-items-center">
                        <div><small><b>Step 2 - PASTE ON AMAZON:</b></small></div>
                        <div className="form-control review"><div className="amazon-logo"></div></div>
                        <a href="https://www.amazon.com/" target="_blank" className="btn btn-success text-uppercase font-weight-bold mt-2" onClick={()=>{handleAmazonFeedback(true)}}><small><b>Click here</b></small></a>
                    </div>
                 </div>
             <div className="text-center py-3"><small><b>After submitting your review, please click the button below to confirm your shipping address and to complete your request.</b></small></div>

            <div className="d-flex justify-content-center">
                <button type="submit" className={`btn btn-${customer.amazonFeedback?'success':'secondary'} text-uppercase font-weight-bold`} onClick={()=>handleStep(customer)}>Next</button>
            </div>
         </div>
     </React.Fragment>
    </div>
)

export default FormStepThree;