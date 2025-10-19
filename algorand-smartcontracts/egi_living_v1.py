"""
EGI Living SmartContract v1.0
Algorand SmartContract for EGI Vivente (Living EGI) with AI integration

@package AlgorandSmartContracts
@author Padmin D. Curtis (AI Partner OS3.0)
@version 1.0.0 (FlorenceEGI - Dual Architecture)
@date 2025-10-19
@purpose SmartContract skeleton for autonomous EGI with AI curator, trigger system, and state management
"""

from pyteal import *

# ============================================================================
# GLOBAL STATE KEYS (stored on-chain)
# ============================================================================

# Core identity and permissions
CREATOR = Bytes("creator")  # Creator wallet address
AUTHORIZED_AGENT = Bytes("auth_agent")  # Oracle wallet authorized to update state

# AI trigger configuration
NEXT_TRIGGER = Bytes("next_trigger")  # Unix timestamp for next AI analysis
TRIGGER_INTERVAL = Bytes("interval")  # Seconds between triggers

# Metadata and licensing
METADATA_HASH = Bytes("meta_hash")  # IPFS hash of current metadata
LICENSE_ID = Bytes("license_id")  # Active license ID
TERMS_HASH = Bytes("terms_hash")  # Hash of terms and conditions

# Exhibition and provenance
EXHIBIT_REFS = Bytes("exhibit_refs")  # Blob: exhibition references (comma-separated IDs)
EPP_ID = Bytes("epp_id")  # Environmental Passport Protocol ID

# Blockchain anchoring
ANCHORING_ROOT = Bytes("anchor_root")  # Merkle root for daily anchoring

# Audit trail
AUDIT_LOG = Bytes("audit_log")  # Blob: audit log entries (latest N events)


# ============================================================================
# HELPER FUNCTIONS
# ============================================================================

def is_creator():
    """Check if transaction sender is the creator"""
    return Txn.sender() == App.globalGet(CREATOR)


def is_authorized_agent():
    """Check if transaction sender is the authorized agent (Oracle)"""
    return Txn.sender() == App.globalGet(AUTHORIZED_AGENT)


def is_creator_or_agent():
    """Check if transaction sender is creator or authorized agent"""
    return Or(is_creator(), is_authorized_agent())


def check_trigger_ready():
    """Check if enough time has passed for next AI trigger"""
    return Global.latest_timestamp() >= App.globalGet(NEXT_TRIGGER)


# ============================================================================
# APPLICATION INITIALIZATION
# ============================================================================

def approval_program():
    """
    Main approval program for EGI Living SmartContract
    Handles initialization, AI triggers, state updates, and queries
    """

    # ========================================================================
    # INITIALIZATION (on_create)
    # ========================================================================
    on_create = Seq([
        # Store creator address
        App.globalPut(CREATOR, Txn.sender()),

        # Store authorized agent address (from application args)
        App.globalPut(AUTHORIZED_AGENT, Txn.application_args[0]),

        # Set trigger interval (from application args, default 86400 = 24h)
        App.globalPut(TRIGGER_INTERVAL, Btoi(Txn.application_args[1])),

        # Set first trigger time (now + interval)
        App.globalPut(
            NEXT_TRIGGER,
            Global.latest_timestamp() + Btoi(Txn.application_args[1])
        ),

        # Initialize metadata hash (from application args)
        App.globalPut(METADATA_HASH, Txn.application_args[2]),

        # Initialize empty/default values
        App.globalPut(LICENSE_ID, Int(0)),
        App.globalPut(TERMS_HASH, Bytes("")),
        App.globalPut(EXHIBIT_REFS, Bytes("")),
        App.globalPut(EPP_ID, Int(0)),
        App.globalPut(ANCHORING_ROOT, Bytes("")),
        App.globalPut(AUDIT_LOG, Bytes("")),

        # Log creation event
        Log(Concat(Bytes("EGI_CREATED:"), Txn.application_args[2])),

        Approve()
    ])

    # ========================================================================
    # REQUEST AI ANALYSIS (callable by anyone, executed if trigger ready)
    # ========================================================================
    request_analysis = Seq([
        # Check if trigger is ready
        Assert(check_trigger_ready()),

        # Update next trigger time
        App.globalPut(
            NEXT_TRIGGER,
            Global.latest_timestamp() + App.globalGet(TRIGGER_INTERVAL)
        ),

        # Log request event (Oracle will listen to this)
        Log(Concat(
            Bytes("REQUEST_ANALYSIS:"),
            Itob(Global.latest_timestamp())
        )),

        Approve()
    ])

    # ========================================================================
    # UPDATE STATE (callable only by authorized agent/Oracle)
    # ========================================================================
    update_state = Seq([
        # Only authorized agent can update
        Assert(is_authorized_agent()),

        # Update metadata hash (from application args)
        App.globalPut(METADATA_HASH, Txn.application_args[1]),

        # Log update event
        Log(Concat(
            Bytes("STATE_UPDATED:"),
            Txn.application_args[1]
        )),

        Approve()
    ])

    # ========================================================================
    # UPDATE LICENSE (callable only by creator)
    # ========================================================================
    update_license = Seq([
        # Only creator can update license
        Assert(is_creator()),

        # Update license ID and terms hash
        App.globalPut(LICENSE_ID, Btoi(Txn.application_args[1])),
        App.globalPut(TERMS_HASH, Txn.application_args[2]),

        # Log license update
        Log(Concat(
            Bytes("LICENSE_UPDATED:"),
            Itob(Btoi(Txn.application_args[1]))
        )),

        Approve()
    ])

    # ========================================================================
    # ADD EXHIBITION REFERENCE (callable by creator or agent)
    # ========================================================================
    add_exhibition = Seq([
        # Creator or agent can add exhibition
        Assert(is_creator_or_agent()),

        # Append exhibition reference to blob
        # Note: In production, implement proper blob management
        App.globalPut(
            EXHIBIT_REFS,
            Concat(
                App.globalGet(EXHIBIT_REFS),
                Bytes(","),
                Txn.application_args[1]
            )
        ),

        # Log exhibition added
        Log(Concat(
            Bytes("EXHIBITION_ADDED:"),
            Txn.application_args[1]
        )),

        Approve()
    ])

    # ========================================================================
    # UPDATE ANCHORING ROOT (callable only by authorized agent)
    # ========================================================================
    update_anchoring = Seq([
        # Only authorized agent can update anchoring
        Assert(is_authorized_agent()),

        # Update anchoring Merkle root
        App.globalPut(ANCHORING_ROOT, Txn.application_args[1]),

        # Log anchoring update
        Log(Concat(
            Bytes("ANCHORING_UPDATED:"),
            Txn.application_args[1]
        )),

        Approve()
    ])

    # ========================================================================
    # QUERY STATE (read-only, callable by anyone)
    # ========================================================================
    query_state = Seq([
        # Log current state (for off-chain reading)
        Log(Concat(
            Bytes("META:"), App.globalGet(METADATA_HASH),
            Bytes("|LIC:"), Itob(App.globalGet(LICENSE_ID)),
            Bytes("|NEXT:"), Itob(App.globalGet(NEXT_TRIGGER))
        )),

        Approve()
    ])

    # ========================================================================
    # DELETE APPLICATION (only creator, emergency use)
    # ========================================================================
    on_delete = Seq([
        Assert(is_creator()),
        Log(Bytes("EGI_TERMINATED")),
        Approve()
    ])

    # ========================================================================
    # ROUTER: Route application calls to appropriate handlers
    # ========================================================================
    program = Cond(
        # On creation
        [Txn.application_id() == Int(0), on_create],

        # On delete
        [Txn.on_completion() == OnComplete.DeleteApplication, on_delete],

        # Application calls (based on first application arg)
        [Txn.application_args[0] == Bytes("request_analysis"), request_analysis],
        [Txn.application_args[0] == Bytes("update_state"), update_state],
        [Txn.application_args[0] == Bytes("update_license"), update_license],
        [Txn.application_args[0] == Bytes("add_exhibition"), add_exhibition],
        [Txn.application_args[0] == Bytes("update_anchoring"), update_anchoring],
        [Txn.application_args[0] == Bytes("query_state"), query_state],

        # Default: reject
        [Int(1), Reject()]
    )

    return program


def clear_program():
    """
    Clear state program (when user opts out)
    Always approve for simplicity (no local state cleanup needed)
    """
    return Approve()


# ============================================================================
# COMPILATION HELPERS
# ============================================================================

def compile_approval():
    """Compile approval program to TEAL"""
    return compileTeal(approval_program(), mode=Mode.Application, version=6)


def compile_clear():
    """Compile clear program to TEAL"""
    return compileTeal(clear_program(), mode=Mode.Application, version=6)


# ============================================================================
# MAIN (for testing compilation)
# ============================================================================

if __name__ == "__main__":
    print("=== EGI Living SmartContract v1.0 ===")
    print("\n--- Approval Program TEAL ---")
    print(compile_approval())
    print("\n--- Clear Program TEAL ---")
    print(compile_clear())

