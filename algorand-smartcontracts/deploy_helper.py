"""
EGI Living SmartContract Deployment Helper
Utilities for deploying and interacting with EGI Living SmartContracts on Algorand

@package AlgorandSmartContracts
@author Padmin D. Curtis (AI Partner OS3.0)
@version 1.0.0 (FlorenceEGI - Dual Architecture)
@date 2025-10-19
"""

import base64
from algosdk import account, mnemonic
from algosdk.v2client import algod
from algosdk.transaction import ApplicationCreateTxn, OnComplete, StateSchema
from algosdk.logic import get_application_address

from egi_living_v1 import compile_approval, compile_clear


# ============================================================================
# CONFIGURATION
# ============================================================================

# Algorand node configuration (testnet by default)
ALGOD_ADDRESS = "https://testnet-api.algonode.cloud"
ALGOD_TOKEN = ""  # Public node, no token needed


# ============================================================================
# COMPILATION FUNCTIONS
# ============================================================================

def compile_program(algod_client, source_code):
    """
    Compile TEAL source code to bytecode

    Args:
        algod_client: Algorand client instance
        source_code: TEAL source code as string

    Returns:
        bytes: Compiled program bytecode
    """
    compile_response = algod_client.compile(source_code)
    return base64.b64decode(compile_response['result'])


# ============================================================================
# DEPLOYMENT FUNCTIONS
# ============================================================================

def deploy_egi_living_contract(
    algod_client,
    deployer_private_key,
    oracle_address,
    trigger_interval=86400,
    metadata_hash="QmDefault"
):
    """
    Deploy EGI Living SmartContract to Algorand

    Args:
        algod_client: Algorand client instance
        deployer_private_key: Private key of deployer (creator) account
        oracle_address: Address of Oracle wallet (authorized agent)
        trigger_interval: Seconds between AI triggers (default 24h)
        metadata_hash: Initial IPFS metadata hash

    Returns:
        dict: Deployment result with app_id, tx_id, and app_address
    """

    # Get deployer address from private key
    deployer_address = account.address_from_private_key(deployer_private_key)

    # Compile programs
    approval_teal = compile_approval()
    clear_teal = compile_clear()

    approval_program = compile_program(algod_client, approval_teal)
    clear_program = compile_program(algod_client, clear_teal)

    # Define global state schema
    global_schema = StateSchema(
        num_uints=8,  # NEXT_TRIGGER, TRIGGER_INTERVAL, LICENSE_ID, EPP_ID, etc.
        num_byte_slices=10  # CREATOR, AUTHORIZED_AGENT, METADATA_HASH, etc.
    )

    # Define local state schema (empty for now)
    local_schema = StateSchema(num_uints=0, num_byte_slices=0)

    # Get suggested transaction parameters
    params = algod_client.suggested_params()

    # Prepare application arguments
    app_args = [
        oracle_address.encode(),  # arg[0]: authorized agent address
        trigger_interval.to_bytes(8, 'big'),  # arg[1]: trigger interval
        metadata_hash.encode()  # arg[2]: initial metadata hash
    ]

    # Create application transaction
    txn = ApplicationCreateTxn(
        sender=deployer_address,
        sp=params,
        on_complete=OnComplete.NoOpOC,
        approval_program=approval_program,
        clear_program=clear_program,
        global_schema=global_schema,
        local_schema=local_schema,
        app_args=app_args
    )

    # Sign transaction
    signed_txn = txn.sign(deployer_private_key)

    # Send transaction
    tx_id = algod_client.send_transaction(signed_txn)

    # Wait for confirmation
    wait_for_confirmation(algod_client, tx_id)

    # Get application ID from transaction
    transaction_response = algod_client.pending_transaction_info(tx_id)
    app_id = transaction_response['application-index']

    # Get application address
    app_address = get_application_address(app_id)

    print(f"✅ EGI Living SmartContract deployed successfully!")
    print(f"   App ID: {app_id}")
    print(f"   App Address: {app_address}")
    print(f"   TX ID: {tx_id}")

    return {
        'app_id': app_id,
        'app_address': app_address,
        'tx_id': tx_id,
        'deployer_address': deployer_address,
        'oracle_address': oracle_address,
        'trigger_interval': trigger_interval
    }


# ============================================================================
# HELPER FUNCTIONS
# ============================================================================

def wait_for_confirmation(algod_client, tx_id, timeout=10):
    """
    Wait for transaction confirmation on Algorand blockchain

    Args:
        algod_client: Algorand client instance
        tx_id: Transaction ID to wait for
        timeout: Maximum rounds to wait

    Returns:
        dict: Transaction confirmation response
    """
    start_round = algod_client.status()["last-round"] + 1
    current_round = start_round

    while current_round < start_round + timeout:
        try:
            pending_txn = algod_client.pending_transaction_info(tx_id)

            if pending_txn.get("confirmed-round", 0) > 0:
                print(f"✅ Transaction confirmed in round {pending_txn['confirmed-round']}")
                return pending_txn

            elif pending_txn.get("pool-error"):
                raise Exception(f"Pool error: {pending_txn['pool-error']}")

        except Exception as e:
            print(f"⚠️ Waiting for confirmation... {e}")

        algod_client.status_after_block(current_round)
        current_round += 1

    raise Exception(f"Transaction not confirmed after {timeout} rounds")


def create_test_account():
    """
    Create a test Algorand account

    Returns:
        dict: Account with address, private_key, and mnemonic
    """
    private_key, address = account.generate_account()
    passphrase = mnemonic.from_private_key(private_key)

    return {
        'address': address,
        'private_key': private_key,
        'mnemonic': passphrase
    }


# ============================================================================
# MAIN (for testing)
# ============================================================================

if __name__ == "__main__":
    print("=== EGI Living SmartContract Deployment Helper ===\n")

    # Initialize Algod client
    algod_client = algod.AlgodClient(ALGOD_TOKEN, ALGOD_ADDRESS)

    print("📊 Network Status:")
    status = algod_client.status()
    print(f"   Last Round: {status['last-round']}")
    print(f"   Time: {status['time-since-last-round']} ms since last round\n")

    print("💡 To deploy a contract, use:")
    print("   1. Fund a testnet account: https://testnet.algoexplorer.io/dispenser")
    print("   2. Call deploy_egi_living_contract() with your private key")
    print("   3. Specify Oracle address and parameters\n")

    # Example: Create test accounts (DO NOT USE IN PRODUCTION)
    print("🧪 Creating test accounts...")
    creator_account = create_test_account()
    oracle_account = create_test_account()

    print(f"   Creator: {creator_account['address']}")
    print(f"   Oracle: {oracle_account['address']}")
    print(f"\n⚠️  IMPORTANT: Fund these accounts on testnet dispenser before deploying!")


