import React from 'react';
import ReactDOM from 'react-dom';
import {withFormik, Form} from "formik";
import axios from 'axios';
import qs from 'qs';
import Select from 'react-select';
import * as Yup from 'yup';

const options = [
    { value: 'Food', label: 'Food' },
    { value: 'Being Fabulous', label: 'Being Fabulous' },
    { value: 'Ken Wheeler', label: 'Ken Wheeler' },
    { value: 'ReasonML', label: 'ReasonML' },
    { value: 'Unicorns', label: 'Unicorns' },
    { value: 'Kittens', label: 'Kittens' },
];

const FormStepTwo = ({
    values,
    errors,
    handleChange,
    handleSubmit,
    onBlur,
    touched
 }) => (
    <div className="formik-container w-100">
     <React.Fragment>
         <div className="form-progress-bar"><div className="form-progress form-progress-w40"><b>40% Completed</b></div></div>
        <Form onSubmit={handleSubmit} className="d-flex flex-column form-step-2">
            <h5 className="text-center pt-3"><b>How satisfied are you with our product?</b></h5>
            <div className="d-flex flex-column pb-3">
                <div className="d-flex justify-content-center align-items-center">
                    <div>Do Not Like It</div>
                    <div className="d-flex mx-2">
                        <input type="radio" name="grad" value="1" className="very-sad"  onChange={handleChange}/>
                        <input type="radio" name="grad" value="2" className="sad"  onChange={handleChange}/>
                        <input type="radio" name="grad" value="3" className="neutral" onChange={handleChange}/>
                        <input type="radio" name="grad" value="4" className="happy"  onChange={handleChange}/>
                        <input type="radio" name="grad" value="5" className="very-happy" onChange={handleChange}/>
                    </div>
                    <div>Love It!</div>
                </div>
                {errors.grad&&touched.grad&&(<small className="text-danger text-center">Select smile</small>)}
            </div>

            <div className="form-group pt-3 d-flex flex-column align-items-center">
                <h5 className="text-center pt-3"><b>Please share your experience with the product here:</b></h5>
                <textarea onBlur={()=>(touched.feedback=true)} className={errors.feedback&&touched.feedback&&('form-control formik-input error') || 'form-control formik-input'} name="feedback" value={values.feedback} onChange={handleChange}/>
                {errors.feedback&&touched.feedback&&(<small className="text-danger text-center">Minimum 25 characters</small>)}
            </div>
            

            <div className="d-flex justify-content-center">
                <button type="submit" className="btn btn-success text-uppercase font-weight-bold">Submit</button>
            </div>

            {errors&&(<p style={{ color: 'red' }}>{errors.general}</p>)}
        </Form>
     </React.Fragment>
    </div>
)

const FormikStepTwo = withFormik({
    mapPropsToValues({feedback, order_id, handleStep }){
        return {
            feedback: feedback || '',
            grad: "",
            handleStep,
            order_id
        }
    },
    // validateOnChange: false,
    validationSchema: Yup.object().shape({
        feedback: Yup.string().min(25).required(),
        grad: Yup.number().required(),
    }),
    handleSubmit(values, actions){
        console.log(values)
        const {grad, feedback} = values;
        //
        const data = {order_id:values.order_id, grad, feedback};
        console.log(data);
        axios.post('/step2', qs.stringify(data),{
            headers: { 'content-type': 'application/x-www-form-urlencoded' },
        })
            .then(function (response) {
                const {customer, isShowAmazon} = JSON.parse(response.data);
                console.log(customer)
                    customer.step = isShowAmazon?1:2;
                    customer.isShowAmazon = isShowAmazon;

                values.handleStep(customer);
            })
            .catch(function (error) {
                console.log(error.response);
                actions.setFieldError(error.response.data.field, error.response.data.message);
            });
    }

})(FormStepTwo);

export default FormikStepTwo;