import React from 'react';
import { Badge, Button, Col, Image, ListGroup, Row } from 'react-bootstrap';
import {FaPlus} from 'react-icons/fa';
import sample from '../../images/01.jpg';

const ProductCard = () => {
    return (
        <div className="mt-3">
            <h4>Category Name <Badge className="badge-primary">33</Badge></h4>
            <ListGroup as="ul">
                <ListGroup.Item as="li">
                    <Row>
                        <Col md={{span:3}}>
                            <Image src={sample} rounded width="100%" />
                        </Col>
                        <Col md={{span:9}}>
                            <h5>Product Name</h5>
                            <p>Per Piece</p>
                            <div>
                                <span className="font-weight-bold float-left">&#8377; 1300</span>
                                <Button variant="outline-primary float-right" size="sm">Add <FaPlus size="10px" style={{marginTop:'-3px' }} /></Button>
                            </div>
                        </Col>
                    </Row>
                </ListGroup.Item>           
                <ListGroup.Item as="li">
                    <Row>
                        <Col md={{span:3}}>
                            <Image src={sample} rounded width="100%" />
                        </Col>
                        <Col md={{span:9}}>
                            <h5>Product Name</h5>
                            <p>Per Piece</p>
                            <div>
                                <span className="font-weight-bold float-left">&#8377; 1300</span>
                                <Button variant="outline-primary float-right" size="sm">Add <FaPlus size="10px" style={{marginTop:'-3px' }} /></Button>
                            </div>
                        </Col>
                    </Row>
                </ListGroup.Item>           
                <ListGroup.Item as="li">
                    <Row>
                        <Col md={{span:3}}>
                            <Image src={sample} rounded width="100%" />
                        </Col>
                        <Col md={{span:9}}>
                            <h5>Product Name</h5>
                            <p>Per Piece</p>
                            <div>
                                <span className="font-weight-bold float-left">&#8377; 1300</span>
                                <Button variant="outline-primary float-right" size="sm">Add <FaPlus size="10px" style={{marginTop:'-3px' }} /></Button>
                            </div>
                        </Col>
                    </Row>
                </ListGroup.Item>           
            </ListGroup>
        </div>
    )
}

export default ProductCard;