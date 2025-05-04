@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Goal Mind Map</h5>
            <div class="btn-group">
                <button class="btn btn-outline-primary btn-sm" id="expand-all">Expand All</button>
                <button class="btn btn-outline-primary btn-sm" id="collapse-all">Collapse All</button>
            </div>
        </div>
        <div class="card-body">
            <div id="mindmap" style="height: 800px;"></div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/vis-network@9.1.2/dist/dist/vis-network.min.css" rel="stylesheet">
<style>
#mindmap {
    border: 1px solid #ddd;
    background: #f8f9fa;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/vis-network@9.1.2/dist/dist/vis-network.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const goals = @json($goals);
    
    // Prepare data for visualization
    const nodes = new vis.DataSet();
    const edges = new vis.DataSet();
    
    // Add central node
    nodes.add({
        id: 'center',
        label: 'My Goals',
        shape: 'dot',
        size: 20,
        color: {
            background: '#3498db',
            border: '#2980b9'
        },
        font: { size: 18 }
    });

    // Process goals and their steps
    goals.forEach(goal => {
        // Add goal node
        nodes.add({
            id: `goal-${goal.id}`,
            label: goal.title,
            shape: 'box',
            color: {
                background: goal.progress === 100 ? '#2ecc71' : '#e74c3c',
                border: goal.progress === 100 ? '#27ae60' : '#c0392b'
            },
            level: 1
        });

        // Connect to center
        edges.add({
            from: 'center',
            to: `goal-${goal.id}`,
            arrows: 'to'
        });

        // Add steps as child nodes
        goal.steps.forEach(step => {
            nodes.add({
                id: `step-${step.id}`,
                label: step.title,
                shape: 'box',
                color: {
                    background: step.completed ? '#2ecc71' : '#f1c40f',
                    border: step.completed ? '#27ae60' : '#f39c12'
                },
                size: 10,
                level: 2
            });

            edges.add({
                from: `goal-${goal.id}`,
                to: `step-${step.id}`,
                arrows: 'to'
            });
        });
    });

    // Create network
    const container = document.getElementById('mindmap');
    const data = { nodes, edges };
    const options = {
        layout: {
            hierarchical: {
                direction: 'UD',
                sortMethod: 'directed',
                nodeSpacing: 150,
                levelSeparation: 150
            }
        },
        physics: {
            enabled: false
        },
        interaction: {
            dragNodes: true,
            dragView: true,
            zoomView: true
        }
    };

    const network = new vis.Network(container, data, options);

    // Handle node clicks
    network.on('click', function(params) {
        if (params.nodes.length) {
            const nodeId = params.nodes[0];
            if (nodeId.startsWith('goal-')) {
                const goalId = nodeId.split('-')[1];
                window.location.href = `/goals/${goalId}`;
            }
        }
    });

    // Handle expand/collapse buttons
    document.getElementById('expand-all').addEventListener('click', function() {
        network.setOptions({ layout: { hierarchical: { enabled: true } } });
    });

    document.getElementById('collapse-all').addEventListener('click', function() {
        network.setOptions({ layout: { hierarchical: { enabled: false } } });
    });
});
</script>
@endpush
@endsection