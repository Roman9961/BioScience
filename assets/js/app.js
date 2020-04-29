import React from 'react';
import ReactDOM from 'react-dom';
require('../css/app.scss');

import FormikStepOne from './components/FormStepOne';
import FormikStepTwo from './components/FormStepTwo';
import FormStepThree from './components/FormStepThree';
import FormikStepFour from './components/FormStepFour';
import FormStepFive from './components/FormStepFive';

import axios from "axios";


class App extends React.Component {
    constructor() {
        super();
        this.state = {
            customer:{},
            products:[],
            formikStep: 1,
            amazonFeedback: false
        };
        this.handleStep = this.handleStep.bind(this);
        this.handleAmazonFeedback = this.handleAmazonFeedback.bind(this)
    }

     handleStep(customer) {
        this.setState(state => ({...state, customer:customer, formikStep: state.formikStep + customer.step}) )
    }

    handleAmazonFeedback(amazonFeedback) {
        this.setState(state => state.customer.amazonFeedback = amazonFeedback);
    }

    componentDidMount() {
        axios.get('/products')
            .then( response => {
                let products = JSON.parse(response.data);
                products = products.map(product=>{
                    product.value = product.id;
                    product.label = product.name;
                    return product;
                })
                this.setState(state =>({...state, products}));
            })
            .catch(error => {
                console.log(error);
            });
    }

    render() {
        return (
            <React.Fragment>
            <div style={{ display: 'flex', justifyContent: 'center'}}>

                    {this.state.formikStep===1&&(<FormikStepOne handleStep={this.handleStep} products = {this.state.products}/>)}
                    {this.state.formikStep===2&&(<FormikStepTwo order_id={this.state.customer.orderId} handleStep={this.handleStep}/>)}
                    {this.state.formikStep===3&&this.state.customer.isShowAmazon&&(<FormStepThree customer={this.state.customer} handleAmazonFeedback ={this.handleAmazonFeedback} handleStep={this.handleStep}/>)}
                    {this.state.formikStep===4&&(<FormikStepFour customer={this.state.customer} handleStep={this.handleStep}/>)}
                    {this.state.formikStep===5&&(<FormStepFive/>)}

            </div>
        </React.Fragment>
    );
    }
}

ReactDOM.render(<App />, document.getElementById('root'));