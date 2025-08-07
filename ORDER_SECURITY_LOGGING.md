# Order Module Security Logging Documentation

## Overview
This document describes the security logging implementation for the ordering module. All order-related activities are now logged to the `security_logs` table for audit and security monitoring purposes.

## New Security Events Added

### Order Events

#### `ORDER_CONFIRM`
- **Level**: INFO
- **Description**: User confirms order details and proceeds to payment
- **Triggered**: When user submits order confirmation form
- **Context Data**:
  - `user_id`: ID of the user placing the order
  - `order_type`: 'pickup' or 'delivery'
  - `amount`: Total order amount
  - `branch_id`: Branch ID (for pickup orders)
  - `delivery_fee`: Delivery fee amount
  - `items_count`: Number of items in cart

#### `ORDER_CREATE_SUCCESS`
- **Level**: INFO
- **Description**: Order successfully saved to database
- **Triggered**: After successful order insertion
- **Context Data**:
  - `user_id`: ID of the user
  - `order_id`: Generated order ID
  - `amount`: Total order amount
  - `order_type`: 'pickup' or 'delivery'
  - `branch_id`: Branch ID (for pickup orders)
  - `delivery_fee`: Delivery fee amount
  - `status`: Order status (usually 'Pending')

#### `ORDER_CREATE_FAIL`
- **Level**: CRITICAL
- **Description**: Order creation failed
- **Triggered**: When order insertion fails
- **Context Data**:
  - `user_id`: ID of the user
  - `error_message`: Database error message
  - `amount`: Total order amount
  - `order_type`: 'pickup' or 'delivery'

#### `ORDER_DETAILS_ADDED`
- **Level**: INFO
- **Description**: Order items successfully saved to order_details table
- **Triggered**: After adding order items
- **Context Data**:
  - `order_id`: Order ID
  - `items_count`: Total number of items in order
  - `items_added`: Number of items successfully added

### Payment Events

#### `PAYMENT_ATTEMPT`
- **Level**: INFO
- **Description**: Payment processing initiated
- **Triggered**: When user submits payment form
- **Context Data**:
  - `user_id`: ID of the user
  - `amount`: Payment amount
  - `payment_method`: Selected payment method
  - `order_type`: 'pickup' or 'delivery'
  - `branch_id`: Branch ID (for pickup orders)

#### `PAYMENT_SUCCESS`
- **Level**: INFO
- **Description**: Payment completed successfully
- **Triggered**: After successful payment processing
- **Context Data**:
  - `user_id`: ID of the user
  - `order_id`: Generated order ID
  - `amount`: Payment amount
  - `payment_method`: Payment method used

#### `PAYMENT_FAIL`
- **Level**: WARN/CRITICAL
- **Description**: Payment failed
- **Triggered**: When payment processing fails
- **Context Data**:
  - `user_id`: ID of the user
  - `reason`: Failure reason ('No payment method selected', 'Insufficient balance', 'Transaction failed')
  - `amount`: Payment amount
  - `payment_method`: Payment method (if selected)
  - `user_balance`: Current user balance (for insufficient balance cases)
  - `error_message`: Detailed error message (for transaction failures)

### Balance Events

#### `BALANCE_DEDUCT_SUCCESS`
- **Level**: INFO
- **Description**: User balance successfully deducted
- **Triggered**: After successful balance deduction
- **Context Data**:
  - `user_id`: ID of the user
  - `amount`: Amount deducted
  - `previous_balance`: Balance before deduction
  - `new_balance`: Balance after deduction

## Implementation Details

### Files Modified

1. **controllers/PaymentController.php**
   - Added SecurityLogger import and instantiation
   - Added logging for payment attempts, successes, and failures
   - Added logging for balance deductions

2. **models/Order.php**
   - Added LoggerTrait usage
   - Added logging for order confirmations and creation
   - Added logging for order details addition

3. **controllers/OrderController.php**
   - Added SecurityLogger import and instantiation
   - Added logging for order confirmations

### Database Table
All events are logged to the `security_logs` table with the following structure:
- `id`: Auto-increment primary key
- `user_id`: User ID (when available)
- `ip_address`: User's IP address
- `level`: Event severity (INFO, WARN, CRITICAL)
- `event_type`: Event type identifier
- `message`: JSON-encoded context data
- `created_at`: Timestamp of the event

### Security Levels Used

- **INFO**: Normal operations (successful orders, payments, confirmations)
- **WARN**: Minor issues (insufficient balance, missing payment method)
- **CRITICAL**: Serious failures (order creation failures, transaction rollbacks)

## Usage Examples

### Successful Order Flow
1. `ORDER_CONFIRM` - User confirms order details
2. `PAYMENT_ATTEMPT` - User initiates payment
3. `BALANCE_DEDUCT_SUCCESS` - Balance deducted successfully
4. `ORDER_CREATE_SUCCESS` - Order saved to database
5. `ORDER_DETAILS_ADDED` - Order items saved
6. `PAYMENT_SUCCESS` - Payment recorded

### Failed Payment Flow
1. `ORDER_CONFIRM` - User confirms order details
2. `PAYMENT_ATTEMPT` - User initiates payment
3. `PAYMENT_FAIL` - Payment fails (insufficient balance/other error)

## Monitoring and Alerts

These logs can be used for:
- **Fraud Detection**: Monitor unusual order patterns
- **Performance Monitoring**: Track payment success rates
- **User Behavior Analysis**: Understand order flow patterns
- **Error Tracking**: Identify and resolve payment issues
- **Audit Compliance**: Maintain transaction records

## Integration with Existing Security System

This implementation follows the same patterns used by your friend's security module:
- Uses the same `SecurityLogger` class
- Follows the same event naming conventions
- Uses consistent severity levels
- Stores data in the same `security_logs` table