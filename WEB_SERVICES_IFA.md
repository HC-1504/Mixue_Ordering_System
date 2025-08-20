## Interface Agreement (IFA) — Admin Orders Web Service

### Webservice Mechanism

- **Protocol**: RESTful (JSON)
- **Function Description**: Exposes order data and allows updating order status. Consumer can retrieve all orders with filters or a single order by ID. Also used by Reports module to compute sales and business insights.
- **Source Module**: Admin Module — Orders
- **Target Module**: Reports Module (internal), any authorized admin/manager consumers

### URL

- Base: `http://{HOST}/Assignment/api/admin`
- List Orders: `GET /orders.php` with optional query parameters
- Get Order by ID: `GET /orders.php?id={orderId}`
- Update Order Status: `PUT /orders.php/{orderId}/status`

### Function Names

- `listOrders`
- `getOrder`
- `updateOrderStatus`

### Web Services Request Parameters (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
| --- | --- | --- | --- | --- |
| search | String | Optional | Free text to search by order id, customer name or email | any string |
| status | String | Optional | Filter by order status | Pending, Preparing, Out for Delivery, Completed, Cancelled |
| limit | Integer | Optional | Page size | positive integer, default 50 |
| offset | Integer | Optional | Offset for pagination | integer, default 0 |
| id | Integer | Mandatory for get-by-id | Order ID when retrieving single order | positive integer |

For status update (PUT `/orders.php/{orderId}/status`):

| Field Name | Field Type | Mandatory/Optional | Description | Format |
| --- | --- | --- | --- | --- |
| status | String | Mandatory | New status to set | same as above |

### Web Services Response Parameters (consume)

Common wrapper:

```
{
  "status": "success" | "error",
  "message"?: string,
  "data"?: object
}
```

For list orders:

```
data: {
  orders: Array<{
    id, user_id, customer_name, customer_email, branch_name,
    total, status, type, created_at, ...
  }>,
  pagination: { total, limit, offset, has_more }
}
```

For get order by ID:

```
data: {
  order: { ...same fields as above },
  details: Array<{ product_id, product_name, quantity, temperature, sugar, unit_price }>
}
```

For update status:

```
data: { order_id, new_status }
```

### Overview of Usage in Module

- **Exposure**: `api/admin/orders.php` exposes the REST endpoints above. It enforces session-based authorization for roles `admin` and `manager` and returns JSON responses.
- **Consumption**: `controllers/admin/ReportController.php` consumes `GET /api/admin/orders.php` to collect orders for generating the Sales and Business reports shown in `admin/reports.php` → `views/admin/reports/index.php`.

### Example Requests

```
GET http://localhost/Assignment/api/admin/orders.php?status=Completed&limit=20
```

```
GET http://localhost/Assignment/api/admin/orders.php?id=1
```

```
PUT http://localhost/Assignment/api/admin/orders.php/1/status
Content-Type: application/json

{ "status": "Preparing" }
```


