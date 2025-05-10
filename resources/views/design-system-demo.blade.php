@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="fas fa-palette me-2"></i>SiCuti Design System</h4>
    </div>
    
    <div class="row g-4">
        <!-- Colors -->
        <div class="col-12">
            <div class="card sicuti-shadow-md">
                <div class="card-header">
                    <h5 class="mb-0">Colors</h5>
                </div>
                <div class="card-body">
                    <h6>Brand Colors</h6>
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <div>
                            <div class="rounded" style="width: 100px; height: 50px; background-color: var(--sicuti-primary);"></div>
                            <div class="mt-2 text-center"><small>Primary</small></div>
                        </div>
                        <div>
                            <div class="rounded" style="width: 100px; height: 50px; background-color: var(--sicuti-primary-dark);"></div>
                            <div class="mt-2 text-center"><small>Primary Dark</small></div>
                        </div>
                        <div>
                            <div class="rounded" style="width: 100px; height: 50px; background-color: var(--sicuti-secondary);"></div>
                            <div class="mt-2 text-center"><small>Secondary</small></div>
                        </div>
                        <div>
                            <div class="rounded" style="width: 100px; height: 50px; background-color: var(--sicuti-success);"></div>
                            <div class="mt-2 text-center"><small>Success</small></div>
                        </div>
                        <div>
                            <div class="rounded" style="width: 100px; height: 50px; background-color: var(--sicuti-info);"></div>
                            <div class="mt-2 text-center"><small>Info</small></div>
                        </div>
                        <div>
                            <div class="rounded" style="width: 100px; height: 50px; background-color: var(--sicuti-warning);"></div>
                            <div class="mt-2 text-center"><small>Warning</small></div>
                        </div>
                        <div>
                            <div class="rounded" style="width: 100px; height: 50px; background-color: var(--sicuti-danger);"></div>
                            <div class="mt-2 text-center"><small>Danger</small></div>
                        </div>
                    </div>
                    
                    <h6>Neutral Colors</h6>
                    <div class="d-flex flex-wrap gap-3">
                        <div>
                            <div class="rounded" style="width: 80px; height: 50px; background-color: var(--sicuti-gray-100);"></div>
                            <div class="mt-2 text-center"><small>Gray 100</small></div>
                        </div>
                        <div>
                            <div class="rounded" style="width: 80px; height: 50px; background-color: var(--sicuti-gray-200);"></div>
                            <div class="mt-2 text-center"><small>Gray 200</small></div>
                        </div>
                        <div>
                            <div class="rounded" style="width: 80px; height: 50px; background-color: var(--sicuti-gray-300);"></div>
                            <div class="mt-2 text-center"><small>Gray 300</small></div>
                        </div>
                        <div>
                            <div class="rounded" style="width: 80px; height: 50px; background-color: var(--sicuti-gray-400);"></div>
                            <div class="mt-2 text-center"><small>Gray 400</small></div>
                        </div>
                        <div>
                            <div class="rounded" style="width: 80px; height: 50px; background-color: var(--sicuti-gray-500);"></div>
                            <div class="mt-2 text-center"><small>Gray 500</small></div>
                        </div>
                        <div>
                            <div class="rounded" style="width: 80px; height: 50px; background-color: var(--sicuti-gray-600); color: white;"></div>
                            <div class="mt-2 text-center"><small>Gray 600</small></div>
                        </div>
                        <div>
                            <div class="rounded" style="width: 80px; height: 50px; background-color: var(--sicuti-gray-700); color: white;"></div>
                            <div class="mt-2 text-center"><small>Gray 700</small></div>
                        </div>
                        <div>
                            <div class="rounded" style="width: 80px; height: 50px; background-color: var(--sicuti-gray-800); color: white;"></div>
                            <div class="mt-2 text-center"><small>Gray 800</small></div>
                        </div>
                        <div>
                            <div class="rounded" style="width: 80px; height: 50px; background-color: var(--sicuti-gray-900); color: white;"></div>
                            <div class="mt-2 text-center"><small>Gray 900</small></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Typography -->
        <div class="col-12">
            <div class="card sicuti-shadow-md">
                <div class="card-header">
                    <h5 class="mb-0">Typography</h5>
                </div>
                <div class="card-body">
                    <h1>Heading 1</h1>
                    <h2>Heading 2</h2>
                    <h3>Heading 3</h3>
                    <h4>Heading 4</h4>
                    <h5>Heading 5</h5>
                    <h6>Heading 6</h6>
                    <p>This is a paragraph with <a href="#">a text link</a> inside it.</p>
                    <p><strong>Bold text</strong> and <em>italic text</em> examples.</p>
                </div>
            </div>
        </div>
        
        <!-- Buttons -->
        <div class="col-md-6">
            <div class="card sicuti-shadow-md">
                <div class="card-header">
                    <h5 class="mb-0">Buttons</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Button Variants</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-primary">Primary</button>
                            <button class="btn btn-secondary">Secondary</button>
                            <button class="btn btn-success">Success</button>
                            <button class="btn btn-danger">Danger</button>
                            <button class="btn btn-warning">Warning</button>
                            <button class="btn btn-info">Info</button>
                            <button class="btn btn-light">Light</button>
                            <button class="btn btn-dark">Dark</button>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6>Button Sizes</h6>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <button class="btn btn-primary btn-sm">Small</button>
                            <button class="btn btn-primary">Default</button>
                            <button class="btn btn-primary btn-lg">Large</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cards -->
        <div class="col-md-6">
            <div class="card sicuti-shadow-md">
                <div class="card-header">
                    <h5 class="mb-0">Cards</h5>
                </div>
                <div class="card-body">
                    <div class="card mb-3">
                        <div class="card-header">
                            Card Header
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Card Title</h5>
                            <p class="card-text">This is an example card with header and footer.</p>
                        </div>
                        <div class="card-footer">
                            Card Footer
                        </div>
                    </div>
                    
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6>Simple Card</h6>
                                    <p class="card-text small">A card without header/footer.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6>Colored Card</h6>
                                    <p class="card-text small mb-0">With background color.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Forms -->
        <div class="col-md-6">
            <div class="card sicuti-shadow-md">
                <div class="card-header">
                    <h5 class="mb-0">Forms</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label for="exampleInput" class="form-label">Text Input</label>
                            <input type="text" class="form-control" id="exampleInput" placeholder="Enter text">
                        </div>
                        
                        <div class="mb-3">
                            <label for="exampleSelect" class="form-label">Select Menu</label>
                            <select class="form-select" id="exampleSelect">
                                <option>Option 1</option>
                                <option>Option 2</option>
                                <option>Option 3</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Checkboxes</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="check1">
                                <label class="form-check-label" for="check1">Checkbox 1</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="check2">
                                <label class="form-check-label" for="check2">Checkbox 2</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Radio Buttons</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="radioOption" id="radio1">
                                <label class="form-check-label" for="radio1">Radio 1</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="radioOption" id="radio2">
                                <label class="form-check-label" for="radio2">Radio 2</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Alerts -->
        <div class="col-md-6">
            <div class="card sicuti-shadow-md">
                <div class="card-header">
                    <h5 class="mb-0">Alerts & Badges</h5>
                </div>
                <div class="card-body">
                    <h6>Alerts</h6>
                    <div class="alert alert-primary" role="alert">
                        This is a primary alert
                    </div>
                    <div class="alert alert-success" role="alert">
                        This is a success alert
                    </div>
                    <div class="alert alert-danger" role="alert">
                        This is a danger alert
                    </div>
                    <div class="alert alert-warning" role="alert">
                        This is a warning alert
                    </div>
                    
                    <h6 class="mt-4">Badges</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary">Primary</span>
                        <span class="badge bg-secondary">Secondary</span>
                        <span class="badge bg-success">Success</span>
                        <span class="badge bg-danger">Danger</span>
                        <span class="badge bg-warning text-dark">Warning</span>
                        <span class="badge bg-info text-dark">Info</span>
                    </div>
                    
                    <h6 class="mt-4">Status Indicators</h6>
                    <div>
                        <span class="status-circle status-pending"></span> Pending
                    </div>
                    <div>
                        <span class="status-circle status-approved"></span> Approved
                    </div>
                    <div>
                        <span class="status-circle status-rejected"></span> Rejected
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tables -->
        <div class="col-12">
            <div class="card sicuti-shadow-md">
                <div class="card-header">
                    <h5 class="mb-0">Tables</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>John Doe</td>
                                    <td>HR</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Jane Smith</td>
                                    <td>Finance</td>
                                    <td><span class="badge bg-warning text-dark">On Leave</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Mike Johnson</td>
                                    <td>IT</td>
                                    <td><span class="badge bg-danger">Inactive</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Shadows and Border Radius -->
        <div class="col-12">
            <div class="card sicuti-shadow-md">
                <div class="card-header">
                    <h5 class="mb-0">Utility Classes</h5>
                </div>
                <div class="card-body">
                    <h6>Shadows</h6>
                    <div class="d-flex flex-wrap gap-4 mb-4">
                        <div class="p-3 sicuti-shadow-sm bg-white" style="width: 120px; height: 100px;">
                            <small>shadow-sm</small>
                        </div>
                        <div class="p-3 sicuti-shadow bg-white" style="width: 120px; height: 100px;">
                            <small>shadow</small>
                        </div>
                        <div class="p-3 sicuti-shadow-md bg-white" style="width: 120px; height: 100px;">
                            <small>shadow-md</small>
                        </div>
                        <div class="p-3 sicuti-shadow-lg bg-white" style="width: 120px; height: 100px;">
                            <small>shadow-lg</small>
                        </div>
                        <div class="p-3 sicuti-shadow-xl bg-white" style="width: 120px; height: 100px;">
                            <small>shadow-xl</small>
                        </div>
                    </div>
                    
                    <h6>Border Radius</h6>
                    <div class="d-flex flex-wrap gap-4">
                        <div class="p-3 bg-primary text-white sicuti-rounded-sm d-flex align-items-center justify-content-center" style="width: 120px; height: 100px;">
                            <small>rounded-sm</small>
                        </div>
                        <div class="p-3 bg-primary text-white sicuti-rounded d-flex align-items-center justify-content-center" style="width: 120px; height: 100px;">
                            <small>rounded</small>
                        </div>
                        <div class="p-3 bg-primary text-white sicuti-rounded-lg d-flex align-items-center justify-content-center" style="width: 120px; height: 100px;">
                            <small>rounded-lg</small>
                        </div>
                        <div class="p-3 bg-primary text-white sicuti-rounded-xl d-flex align-items-center justify-content-center" style="width: 120px; height: 100px;">
                            <small>rounded-xl</small>
                        </div>
                        <div class="p-3 bg-primary text-white sicuti-rounded-2xl d-flex align-items-center justify-content-center" style="width: 120px; height: 100px;">
                            <small>rounded-2xl</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 