<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workflow Builder - Digital Marketing SaaS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; overflow: hidden; }
        
        .workflow-builder {
            display: flex;
            height: calc(100vh - 56px);
        }
        
        /* Node Palette */
        .node-palette {
            width: 220px;
            background: #1e282c;
            color: #b8c7ce;
            overflow-y: auto;
            padding: 10px;
            border-right: 1px solid #3c4850;
        }
        
        .palette-section {
            margin-bottom: 15px;
        }
        
        .palette-section h4 {
            font-size: 11px;
            text-transform: uppercase;
            color: #6c7a80;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        
        .palette-node {
            display: flex;
            align-items: center;
            padding: 8px 10px;
            margin-bottom: 4px;
            background: #2c3b41;
            border-radius: 4px;
            cursor: grab;
            transition: all 0.2s;
            font-size: 12px;
        }
        
        .palette-node:hover {
            background: #3c4850;
            transform: translateX(2px);
        }
        
        .palette-node i {
            width: 24px;
            margin-right: 8px;
            font-size: 14px;
        }
        
        .palette-node.trigger i { color: #28a745; }
        .palette-node.action i { color: #007bff; }
        .palette-node.condition i { color: #ffc107; }
        .palette-node.util i { color: #6c757d; }
        
        /* Canvas */
        .canvas-container {
            flex: 1;
            position: relative;
            background: #f4f6f9;
            overflow: hidden;
        }
        
        #workflow-canvas {
            width: 100%;
            height: 100%;
            cursor: default;
        }
        
        /* Workflow Nodes */
        .workflow-node {
            position: absolute;
            min-width: 180px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            cursor: move;
            user-select: none;
            transition: box-shadow 0.2s;
        }
        
        .workflow-node:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        
        .workflow-node.selected {
            box-shadow: 0 0 0 2px #007bff, 0 4px 16px rgba(0,0,0,0.15);
        }
        
        .workflow-node.executing {
            box-shadow: 0 0 0 2px #28a745, 0 0 20px rgba(40, 167, 69, 0.4);
            animation: pulse 1.5s infinite;
        }
        
        .workflow-node.failed {
            box-shadow: 0 0 0 2px #dc3545, 0 0 20px rgba(220, 53, 69, 0.4);
        }
        
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 2px #28a745, 0 0 20px rgba(40, 167, 69, 0.4); }
            50% { box-shadow: 0 0 0 4px #28a745, 0 0 30px rgba(40, 167, 69, 0.6); }
        }
        
        .node-header {
            padding: 10px 12px;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            color: white;
            font-weight: 600;
            font-size: 12px;
        }
        
        .node-header.trigger { background: linear-gradient(135deg, #28a745, #20c997); }
        .node-header.action { background: linear-gradient(135deg, #007bff, #6610f2); }
        .node-header.condition { background: linear-gradient(135deg, #ffc107, #fd7e14); }
        .node-header.util { background: linear-gradient(135deg, #6c757d, #495057); }
        
        .node-header i {
            margin-right: 8px;
            font-size: 14px;
        }
        
        .node-body {
            padding: 10px 12px;
            font-size: 11px;
            color: #6c757d;
        }
        
        .node-port {
            position: absolute;
            width: 12px;
            height: 12px;
            background: #fff;
            border: 2px solid #007bff;
            border-radius: 50%;
            cursor: crosshair;
        }
        
        .node-port.input {
            left: -6px;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .node-port.output {
            right: -6px;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .node-port:hover {
            background: #007bff;
            transform: translateY(-50%) scale(1.2);
        }
        
        /* Connections */
        .connection-line {
            fill: none;
            stroke: #007bff;
            stroke-width: 2;
            pointer-events: stroke;
        }
        
        .connection-line.executing {
            stroke: #28a745;
            stroke-width: 3;
            animation: dash 1s linear infinite;
        }
        
        .connection-line.failed {
            stroke: #dc3545;
        }
        
        @keyframes dash {
            to { stroke-dashoffset: -20; }
        }
        
        /* Toolbar */
        .builder-toolbar {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.15);
            display: flex;
            padding: 5px;
            z-index: 100;
        }
        
        .toolbar-btn {
            padding: 8px 12px;
            border: none;
            background: transparent;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            color: #495057;
            transition: all 0.2s;
        }
        
        .toolbar-btn:hover {
            background: #e9ecef;
        }
        
        .toolbar-btn.primary {
            background: #007bff;
            color: white;
        }
        
        .toolbar-btn.primary:hover {
            background: #0056b3;
        }
        
        .toolbar-btn.success {
            background: #28a745;
            color: white;
        }
        
        .toolbar-divider {
            width: 1px;
            background: #dee2e6;
            margin: 5px 8px;
        }
        
        /* Properties Panel */
        .properties-panel {
            width: 280px;
            background: white;
            border-left: 1px solid #dee2e6;
            overflow-y: auto;
            padding: 15px;
        }
        
        .properties-panel h4 {
            font-size: 14px;
            margin-bottom: 15px;
            color: #495057;
        }
        
        .form-group {
            margin-bottom: 12px;
        }
        
        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 4px;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 6px 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }
        
        /* Execution Log */
        .execution-log {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 350px;
            max-height: 200px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.15);
            overflow: hidden;
            display: none;
        }
        
        .execution-log.show {
            display: block;
        }
        
        .log-header {
            padding: 10px 15px;
            background: #343a40;
            color: white;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .log-body {
            max-height: 150px;
            overflow-y: auto;
            padding: 10px;
        }
        
        .log-entry {
            font-size: 11px;
            padding: 4px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .log-entry.success { color: #28a745; }
        .log-entry.error { color: #dc3545; }
        .log-entry.info { color: #17a2b8; }
        
        /* Templates Modal */
        .template-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .template-card:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.1);
        }
        
        .template-card h5 {
            font-size: 13px;
            margin-bottom: 5px;
        }
        
        .template-card p {
            font-size: 11px;
            color: #6c757d;
            margin: 0;
        }
        
        /* Zoom Controls */
        .zoom-controls {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            display: flex;
        }
        
        .zoom-btn {
            padding: 8px 12px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 14px;
        }
        
        .zoom-btn:hover {
            background: #f0f0f0;
        }
        
        .zoom-level {
            padding: 8px 12px;
            font-size: 11px;
            color: #6c757d;
            min-width: 50px;
            text-align: center;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/dashboard" class="nav-link">Dashboard</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/workflows" class="nav-link active">Workflows</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/logout"><i class="fas fa-sign-out-alt"></i></a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="workflow-builder">
            <!-- Node Palette -->
            <div class="node-palette">
                <div class="palette-section">
                    <h4><i class="fas fa-bolt"></i> Triggers</h4>
                    <div class="palette-node trigger" draggable="true" data-type="trigger" data-trigger="post_published">
                        <i class="fas fa-pen-fancy"></i> Post Published
                    </div>
                    <div class="palette-node trigger" draggable="true" data-type="trigger" data-trigger="comment_received">
                        <i class="fas fa-comment"></i> Comment Received
                    </div>
                    <div class="palette-node trigger" draggable="true" data-type="trigger" data-trigger="mention_received">
                        <i class="fas fa-at"></i> Mention Received
                    </div>
                    <div class="palette-node trigger" draggable="true" data-type="trigger" data-trigger="message_received">
                        <i class="fas fa-envelope"></i> DM Received
                    </div>
                    <div class="palette-node trigger" draggable="true" data-type="trigger" data-trigger="schedule">
                        <i class="fas fa-clock"></i> Schedule
                    </div>
                    <div class="palette-node trigger" draggable="true" data-type="trigger" data-trigger="webhook">
                        <i class="fas fa-globe"></i> Webhook
                    </div>
                </div>
                
                <div class="palette-section">
                    <h4><i class="fas fa-cogs"></i> Actions</h4>
                    <div class="palette-node action" draggable="true" data-type="action" data-action="send_notification">
                        <i class="fas fa-bell"></i> Send Notification
                    </div>
                    <div class="palette-node action" draggable="true" data-type="action" data-action="auto_reply">
                        <i class="fas fa-reply"></i> Auto Reply
                    </div>
                    <div class="palette-node action" draggable="true" data-type="action" data-action="create_post">
                        <i class="fas fa-plus"></i> Create Post
                    </div>
                    <div class="palette-node action" draggable="true" data-type="action" data-action="schedule_post">
                        <i class="fas fa-calendar"></i> Schedule Post
                    </div>
                    <div class="palette-node action" draggable="true" data-type="action" data-action="ai_generate">
                        <i class="fas fa-robot"></i> AI Generate
                    </div>
                    <div class="palette-node action" draggable="true" data-type="action" data-action="tag_client">
                        <i class="fas fa-tag"></i> Tag Client
                    </div>
                    <div class="palette-node action" draggable="true" data-type="action" data-action="send_email">
                        <i class="fas fa-paper-plane"></i> Send Email
                    </div>
                    <div class="palette-node action" draggable="true" data-type="action" data-action="webhook_call">
                        <i class="fas fa-external-link-alt"></i> Webhook Call
                    </div>
                </div>
                
                <div class="palette-section">
                    <h4><i class="fas fa-code-branch"></i> Logic</h4>
                    <div class="palette-node condition" draggable="true" data-type="condition" data-condition="if">
                        <i class="fas fa-question"></i> Condition
                    </div>
                    <div class="palette-node util" draggable="true" data-type="util" data-util="delay">
                        <i class="fas fa-hourglass-half"></i> Delay
                    </div>
                    <div class="palette-node util" draggable="true" data-type="util" data-util="loop">
                        <i class="fas fa-redo"></i> Loop
                    </div>
                </div>
            </div>
            
            <!-- Canvas -->
            <div class="canvas-container">
                <canvas id="workflow-canvas"></canvas>
                
                <!-- Toolbar -->
                <div class="builder-toolbar">
                    <button class="toolbar-btn" onclick="workflowBuilder.undo()"><i class="fas fa-undo"></i></button>
                    <button class="toolbar-btn" onclick="workflowBuilder.redo()"><i class="fas fa-redo"></i></button>
                    <div class="toolbar-divider"></div>
                    <button class="toolbar-btn" onclick="workflowBuilder.zoomIn()"><i class="fas fa-search-plus"></i></button>
                    <button class="toolbar-btn" onclick="workflowBuilder.zoomOut()"><i class="fas fa-search-minus"></i></button>
                    <button class="toolbar-btn" onclick="workflowBuilder.fitView()"><i class="fas fa-expand"></i></button>
                    <div class="toolbar-divider"></div>
                    <button class="toolbar-btn" onclick="workflowBuilder.testWorkflow()"><i class="fas fa-play"></i> Test</button>
                    <button class="toolbar-btn" onclick="workflowBuilder.clearCanvas()"><i class="fas fa-trash"></i> Clear</button>
                    <div class="toolbar-divider"></div>
                    <button class="toolbar-btn primary" onclick="workflowBuilder.saveWorkflow()"><i class="fas fa-save"></i> Save</button>
                    <button class="toolbar-btn success" onclick="workflowBuilder.activateWorkflow()"><i class="fas fa-power-off"></i> Activate</button>
                </div>
                
                <!-- Zoom Controls -->
                <div class="zoom-controls">
                    <button class="zoom-btn" onclick="workflowBuilder.zoomOut()"><i class="fas fa-minus"></i></button>
                    <span class="zoom-level" id="zoom-level">100%</span>
                    <button class="zoom-btn" onclick="workflowBuilder.zoomIn()"><i class="fas fa-plus"></i></button>
                </div>
                
                <!-- Execution Log -->
                <div class="execution-log" id="execution-log">
                    <div class="log-header">
                        <span><i class="fas fa-terminal"></i> Execution Log</span>
                        <button class="toolbar-btn" onclick="workflowBuilder.toggleLog()"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="log-body" id="log-body"></div>
                </div>
            </div>
            
            <!-- Properties Panel -->
            <div class="properties-panel" id="properties-panel">
                <h4><i class="fas fa-sliders-h"></i> Properties</h4>
                <p class="text-muted text-center py-4">Select a node to edit its properties</p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script>
    class WorkflowBuilder {
        constructor() {
            this.canvas = document.getElementById('workflow-canvas');
            this.ctx = this.canvas.getContext('2d');
            this.nodes = [];
            this.connections = [];
            this.selectedNode = null;
            this.isDragging = false;
            this.dragOffset = { x: 0, y: 0 };
            this.zoom = 1;
            this.pan = { x: 0, y: 0 };
            this.history = [];
            this.historyIndex = -1;
            this.nodeIdCounter = 0;
            
            this.init();
        }
        
        init() {
            this.resizeCanvas();
            window.addEventListener('resize', () => this.resizeCanvas());
            
            // Drag and drop from palette
            document.querySelectorAll('.palette-node').forEach(node => {
                node.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('nodeType', node.dataset.type);
                    e.dataTransfer.setData('nodeData', JSON.stringify(node.dataset));
                });
            });
            
            this.canvas.addEventListener('dragover', (e) => e.preventDefault());
            this.canvas.addEventListener('drop', (e) => this.handleDrop(e));
            
            // Canvas interactions
            this.canvas.addEventListener('mousedown', (e) => this.handleMouseDown(e));
            this.canvas.addEventListener('mousemove', (e) => this.handleMouseMove(e));
            this.canvas.addEventListener('mouseup', (e) => this.handleMouseUp(e));
            this.canvas.addEventListener('dblclick', (e) => this.handleDoubleClick(e));
            
            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => this.handleKeyDown(e));
            
            // Initial draw
            this.draw();
        }
        
        resizeCanvas() {
            const container = this.canvas.parentElement;
            this.canvas.width = container.clientWidth;
            this.canvas.height = container.clientHeight;
            this.draw();
        }
        
        handleDrop(e) {
            e.preventDefault();
            const rect = this.canvas.getBoundingClientRect();
            const x = (e.clientX - rect.left - this.pan.x) / this.zoom;
            const y = (e.clientY - rect.top - this.pan.y) / this.zoom;
            
            const nodeType = e.dataTransfer.getData('nodeType');
            const nodeData = JSON.parse(e.dataTransfer.getData('nodeData'));
            
            this.addNode(nodeType, nodeData, x, y);
        }
        
        addNode(type, data, x, y) {
            const node = {
                id: ++this.nodeIdCounter,
                type: type,
                subtype: data[type === 'trigger' ? 'trigger' : type === 'action' ? 'action' : type === 'condition' ? 'condition' : 'util'],
                x: x,
                y: y,
                config: {},
                label: this.getNodeLabel(type, data)
            };
            
            this.nodes.push(node);
            this.saveHistory();
            this.draw();
            this.selectNode(node);
        }
        
        getNodeLabel(type, data) {
            const labels = {
                trigger: {
                    post_published: 'Post Published',
                    comment_received: 'Comment Received',
                    mention_received: 'Mention Received',
                    message_received: 'DM Received',
                    schedule: 'Schedule',
                    webhook: 'Webhook'
                },
                action: {
                    send_notification: 'Send Notification',
                    auto_reply: 'Auto Reply',
                    create_post: 'Create Post',
                    schedule_post: 'Schedule Post',
                    ai_generate: 'AI Generate',
                    tag_client: 'Tag Client',
                    send_email: 'Send Email',
                    webhook_call: 'Webhook Call'
                },
                condition: {
                    if: 'Condition'
                },
                util: {
                    delay: 'Delay',
                    loop: 'Loop'
                }
            };
            
            const key = data[type === 'trigger' ? 'trigger' : type === 'action' ? 'action' : type === 'condition' ? 'condition' : 'util'];
            return labels[type]?.[key] || 'Unknown';
        }
        
        draw() {
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
            
            this.ctx.save();
            this.ctx.translate(this.pan.x, this.pan.y);
            this.ctx.scale(this.zoom, this.zoom);
            
            // Draw grid
            this.drawGrid();
            
            // Draw connections
            this.drawConnections();
            
            // Draw nodes
            this.drawNodes();
            
            this.ctx.restore();
        }
        
        drawGrid() {
            const gridSize = 20;
            this.ctx.strokeStyle = '#e9ecef';
            this.ctx.lineWidth = 0.5;
            
            for (let x = 0; x < this.canvas.width / this.zoom; x += gridSize) {
                this.ctx.beginPath();
                this.ctx.moveTo(x, 0);
                this.ctx.lineTo(x, this.canvas.height / this.zoom);
                this.ctx.stroke();
            }
            
            for (let y = 0; y < this.canvas.height / this.zoom; y += gridSize) {
                this.ctx.beginPath();
                this.ctx.moveTo(0, y);
                this.ctx.lineTo(this.canvas.width / this.zoom, y);
                this.ctx.stroke();
            }
        }
        
        drawNodes() {
            this.nodes.forEach(node => {
                const x = node.x;
                const y = node.y;
                const width = 180;
                const height = 60;
                
                // Node shadow
                this.ctx.fillStyle = 'rgba(0,0,0,0.1)';
                this.ctx.beginPath();
                this.ctx.roundRect(x + 2, y + 2, width, height, 8);
                this.ctx.fill();
                
                // Node body
                this.ctx.fillStyle = 'white';
                this.ctx.beginPath();
                this.ctx.roundRect(x, y, width, height, 8);
                this.ctx.fill();
                
                // Node border
                this.ctx.strokeStyle = node === this.selectedNode ? '#007bff' : '#dee2e6';
                this.ctx.lineWidth = node === this.selectedNode ? 2 : 1;
                this.ctx.stroke();
                
                // Header
                const headerColor = this.getNodeColor(node.type);
                this.ctx.fillStyle = headerColor;
                this.ctx.beginPath();
                this.ctx.roundRect(x, y, width, 24, [8, 8, 0, 0]);
                this.ctx.fill();
                
                // Header text
                this.ctx.fillStyle = 'white';
                this.ctx.font = 'bold 11px sans-serif';
                this.ctx.fillText(node.label, x + 8, y + 16);
                
                // Body text
                this.ctx.fillStyle = '#6c757d';
                this.ctx.font = '10px sans-serif';
                const configText = Object.keys(node.config).length > 0 ? 'Configured' : 'Double-click to configure';
                this.ctx.fillText(configText, x + 8, y + 42);
                
                // Input port
                if (node.type !== 'trigger') {
                    this.ctx.fillStyle = '#007bff';
                    this.ctx.beginPath();
                    this.ctx.arc(x, y + height / 2, 5, 0, Math.PI * 2);
                    this.ctx.fill();
                }
                
                // Output port
                if (node.type !== 'condition') {
                    this.ctx.fillStyle = '#007bff';
                    this.ctx.beginPath();
                    this.ctx.arc(x + width, y + height / 2, 5, 0, Math.PI * 2);
                    this.ctx.fill();
                }
            });
        }
        
        getNodeColor(type) {
            const colors = {
                trigger: '#28a745',
                action: '#007bff',
                condition: '#ffc107',
                util: '#6c757d'
            };
            return colors[type] || '#6c757d';
        }
        
        drawConnections() {
            this.connections.forEach(conn => {
                const fromNode = this.nodes.find(n => n.id === conn.from);
                const toNode = this.nodes.find(n => n.id === conn.to);
                
                if (!fromNode || !toNode) return;
                
                const x1 = fromNode.x + 180;
                const y1 = fromNode.y + 30;
                const x2 = toNode.x;
                const y2 = toNode.y + 30;
                
                // Bezier curve
                this.ctx.strokeStyle = '#007bff';
                this.ctx.lineWidth = 2;
                this.ctx.beginPath();
                this.ctx.moveTo(x1, y1);
                this.ctx.bezierCurveTo(x1 + 50, y1, x2 - 50, y2, x2, y2);
                this.ctx.stroke();
                
                // Arrow
                this.ctx.fillStyle = '#007bff';
                this.ctx.beginPath();
                this.ctx.moveTo(x2, y2);
                this.ctx.lineTo(x2 - 8, y2 - 4);
                this.ctx.lineTo(x2 - 8, y2 + 4);
                this.ctx.closePath();
                this.ctx.fill();
            });
        }
        
        handleMouseDown(e) {
            const rect = this.canvas.getBoundingClientRect();
            const x = (e.clientX - rect.left - this.pan.x) / this.zoom;
            const y = (e.clientY - rect.top - this.pan.y) / this.zoom;
            
            const clickedNode = this.nodes.find(n => 
                x >= n.x && x <= n.x + 180 && y >= n.y && y <= n.y + 60
            );
            
            if (clickedNode) {
                this.selectNode(clickedNode);
                this.isDragging = true;
                this.dragOffset = { x: x - clickedNode.x, y: y - clickedNode.y };
            } else {
                this.selectedNode = null;
                this.draw();
            }
        }
        
        handleMouseMove(e) {
            if (this.isDragging && this.selectedNode) {
                const rect = this.canvas.getBoundingClientRect();
                const x = (e.clientX - rect.left - this.pan.x) / this.zoom;
                const y = (e.clientY - rect.top - this.pan.y) / this.zoom;
                
                this.selectedNode.x = x - this.dragOffset.x;
                this.selectedNode.y = y - this.dragOffset.y;
                this.draw();
            }
        }
        
        handleMouseUp(e) {
            if (this.isDragging) {
                this.isDragging = false;
                this.saveHistory();
            }
        }
        
        handleDoubleClick(e) {
            if (this.selectedNode) {
                this.showNodeConfig(this.selectedNode);
            }
        }
        
        handleKeyDown(e) {
            if (e.key === 'Delete' && this.selectedNode) {
                this.deleteNode(this.selectedNode);
            }
            if (e.ctrlKey && e.key === 'z') {
                this.undo();
            }
            if (e.ctrlKey && e.key === 'y') {
                this.redo();
            }
        }
        
        selectNode(node) {
            this.selectedNode = node;
            this.draw();
            this.showPropertiesPanel(node);
        }
        
        showPropertiesPanel(node) {
            const panel = document.getElementById('properties-panel');
            panel.innerHTML = `
                <h4><i class="fas fa-sliders-h"></i> ${node.label}</h4>
                <div class="form-group">
                    <label>Label</label>
                    <input type="text" value="${node.label}" onchange="workflowBuilder.updateNodeLabel(this.value)">
                </div>
                ${this.getNodeConfigFields(node)}
                <button class="btn btn-danger btn-sm mt-3" onclick="workflowBuilder.deleteNode(workflowBuilder.selectedNode)">
                    <i class="fas fa-trash"></i> Delete Node
                </button>
            `;
        }
        
        getNodeConfigFields(node) {
            const configs = {
                trigger: {
                    post_published: '<div class="form-group"><label>Platform</label><select class="form-control"><option>Any</option><option>Facebook</option><option>Instagram</option><option>Twitter</option></select></div>',
                    schedule: '<div class="form-group"><label>Cron Expression</label><input type="text" placeholder="0 9 * * *" class="form-control"></div>',
                    webhook: '<div class="form-group"><label>Webhook URL</label><input type="text" placeholder="https://..." class="form-control"></div>'
                },
                action: {
                    send_notification: '<div class="form-group"><label>Message</label><textarea class="form-control" rows="2">New post published!</textarea></div>',
                    auto_reply: '<div class="form-group"><label>Reply Message</label><textarea class="form-control" rows="2">Thanks for reaching out!</textarea></div>',
                    create_post: '<div class="form-group"><label>Content</label><textarea class="form-control" rows="3"></textarea></div>',
                    ai_generate: '<div class="form-group"><label>Prompt</label><textarea class="form-control" rows="3">Generate a social post about...</textarea></div>',
                    send_email: '<div class="form-group"><label>To</label><input type="email" class="form-control"></div><div class="form-group"><label>Subject</label><input type="text" class="form-control"></div>',
                    webhook_call: '<div class="form-group"><label>URL</label><input type="url" class="form-control"></div><div class="form-group"><label>Method</label><select class="form-control"><option>POST</option><option>GET</option><option>PUT</option></select></div>'
                },
                condition: {
                    if: '<div class="form-group"><label>Field</label><input type="text" placeholder="platform" class="form-control"></div><div class="form-group"><label>Operator</label><select class="form-control"><option>equals</option><option>contains</option><option>not empty</option></select></div><div class="form-group"><label>Value</label><input type="text" class="form-control"></div>'
                },
                util: {
                    delay: '<div class="form-group"><label>Seconds</label><input type="number" value="5" class="form-control"></div>',
                    loop: '<div class="form-group"><label>Iterations</label><input type="number" value="5" class="form-control"></div>'
                }
            };
            
            return configs[node.type]?.[node.subtype] || '<p class="text-muted">No additional configuration</p>';
        }
        
        updateNodeLabel(label) {
            if (this.selectedNode) {
                this.selectedNode.label = label;
                this.draw();
            }
        }
        
        showNodeConfig(node) {
            this.showPropertiesPanel(node);
        }
        
        deleteNode(node) {
            this.nodes = this.nodes.filter(n => n.id !== node.id);
            this.connections = this.connections.filter(c => c.from !== node.id && c.to !== node.id);
            this.selectedNode = null;
            this.saveHistory();
            this.draw();
            document.getElementById('properties-panel').innerHTML = '<h4><i class="fas fa-sliders-h"></i> Properties</h4><p class="text-muted text-center py-4">Select a node to edit its properties</p>';
        }
        
        saveHistory() {
            this.history = this.history.slice(0, this.historyIndex + 1);
            this.history.push(JSON.stringify({ nodes: this.nodes, connections: this.connections }));
            this.historyIndex++;
        }
        
        undo() {
            if (this.historyIndex > 0) {
                this.historyIndex--;
                const state = JSON.parse(this.history[this.historyIndex]);
                this.nodes = state.nodes;
                this.connections = state.connections;
                this.draw();
            }
        }
        
        redo() {
            if (this.historyIndex < this.history.length - 1) {
                this.historyIndex++;
                const state = JSON.parse(this.history[this.historyIndex]);
                this.nodes = state.nodes;
                this.connections = state.connections;
                this.draw();
            }
        }
        
        zoomIn() {
            this.zoom = Math.min(this.zoom * 1.2, 2);
            this.updateZoom();
        }
        
        zoomOut() {
            this.zoom = Math.max(this.zoom / 1.2, 0.5);
            this.updateZoom();
        }
        
        fitView() {
            this.zoom = 1;
            this.pan = { x: 0, y: 0 };
            this.updateZoom();
        }
        
        updateZoom() {
            document.getElementById('zoom-level').textContent = Math.round(this.zoom * 100) + '%';
            this.draw();
        }
        
        testWorkflow() {
            const log = document.getElementById('execution-log');
            const logBody = document.getElementById('log-body');
            log.classList.add('show');
            logBody.innerHTML = '';
            
            this.log('Starting workflow test...', 'info');
            
            // Simulate execution
            let delay = 500;
            this.nodes.forEach((node, i) => {
                setTimeout(() => {
                    this.log(`Executing: ${node.label}`, 'success');
                }, delay * (i + 1));
            });
            
            setTimeout(() => {
                this.log('Workflow test completed successfully!', 'success');
            }, delay * (this.nodes.length + 1));
        }
        
        log(message, type = 'info') {
            const logBody = document.getElementById('log-body');
            const entry = document.createElement('div');
            entry.className = `log-entry ${type}`;
            entry.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle"></i> ${message}`;
            logBody.appendChild(entry);
            logBody.scrollTop = logBody.scrollHeight;
        }
        
        toggleLog() {
            document.getElementById('execution-log').classList.remove('show');
        }
        
        clearCanvas() {
            if (confirm('Clear all nodes and connections?')) {
                this.nodes = [];
                this.connections = [];
                this.selectedNode = null;
                this.saveHistory();
                this.draw();
            }
        }
        
        saveWorkflow() {
            const name = prompt('Workflow name:');
            if (!name) return;
            
            const workflow = {
                name: name,
                nodes: this.nodes,
                connections: this.connections
            };
            
            console.log('Saving workflow:', workflow);
            alert('Workflow "' + name + '" saved! (Check console for data)');
        }
        
        activateWorkflow() {
            if (this.nodes.length === 0) {
                alert('Add at least one node before activating.');
                return;
            }
            alert('Workflow activated! It will now run automatically.');
        }
    }
    
    // Initialize
    const workflowBuilder = new WorkflowBuilder();
    </script>
</body>
</html>
